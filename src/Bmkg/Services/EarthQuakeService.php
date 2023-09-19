<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use TheBachtiarz\Base\App\Helpers\CacheHelper;
use TheBachtiarz\Base\App\Interfaces\Helpers\ResponseInterface;
use TheBachtiarz\Base\App\Libraries\Curl\Data\CurlResponse;
use TheBachtiarz\Base\App\Libraries\Curl\Data\CurlResponseInterface;
use TheBachtiarz\Base\App\Libraries\Paginator\Params\PaginatorParam;
use TheBachtiarz\Base\App\Libraries\Search\Params\QuerySearchInput;
use TheBachtiarz\Base\App\Libraries\Search\Params\QuerySearchInputInterface;
use TheBachtiarz\Base\App\Services\AbstractService;
use TheBachtiarz\Party\Bmkg\Interfaces\Configs\BmkgConfigInterface;
use TheBachtiarz\Party\Bmkg\Interfaces\Models\EarthQuakeInterface;
use TheBachtiarz\Party\Bmkg\Libraries\CurlBmkgLibrary;
use TheBachtiarz\Party\Bmkg\Models\EarthQuake;
use TheBachtiarz\Party\Bmkg\Repositories\EarthQuakeRepository;
use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;
use TheBachtiarz\Party\Contact\Repositories\ContactRepository;
use TheBachtiarz\WhatsApp\Services\WhatsAppMessageService;
use Throwable;

use function app;
use function array_map;
use function array_merge;
use function assert;
use function collect;
use function explode;
use function is_int;
use function json_encode;
use function mb_strlen;
use function serialize;
use function sprintf;
use function unserialize;

class EarthQuakeService extends AbstractService
{
    /**
     * Cache earthquake name
     */
    protected const CACHE_INFO_NAME = 'earthquakeinfo';

    /**
     * Notify to groups
     *
     * @var array
     */
    protected array $notifyGroups = [];

    /**
     * Notify to individuals
     *
     * @var array
     */
    protected array $notifyIndividuals = [];

    /**
     * Use contact(s) from config
     */
    protected bool $useContacts = false;

    /**
     * Dummy hash
     */
    private string|null $dummyHash = null;

    /**
     * Notify message
     */
    private string $notifyMessage = '';

    /**
     * Constructor
     */
    public function __construct(
        protected CurlBmkgLibrary $curlBmkgLibrary,
        protected EarthQuakeRepository $earthQuakeRepository,
        protected ContactRepository $contactRepository,
        protected WhatsAppMessageService $whatsappMessageService,
    ) {
        $this->dummyHash     = Hash::make(value: 'default');
        $this->notifyMessage = '';
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->dummyHash     = null;
        $this->notifyMessage = '';
    }

    // ? Public Methods

    /**
     * Get earthquake informations
     *
     * @return array
     */
    public function getInfo(): array
    {
        try {
            $process = $this->curlBmkgLibrary->execute(CurlBmkgLibrary::EARTHQUAKE_LIVE_INFO);

            if ($process->getStatus() !== 'success') {
                throw new Exception($process->getMessage());
            }

            CacheHelper::setCache(
                cacheName: self::CACHE_INFO_NAME,
                value: Hash::make(json_encode($process->getData())),
            );

            $this->setResponseData(message: 'Eartquake informations', data: $process->getData(), httpCode: 200);

            return $this->serviceResult(status: true, message: 'Eartquake informations', data: $process->getData());
        } catch (Throwable $th) {
            $this->log($th);
            $this->setResponseData(message: $th->getMessage(), httpCode: 202);

            return $this->serviceResult(message: $th->getMessage());
        }
    }

    /**
     * Push notification about earchquake
     *
     * @return array
     */
    public function pushNotify(): array
    {
        try {
            $earthQuakeInfo = $this->curlBmkgLibrary->execute(CurlBmkgLibrary::EARTHQUAKE_LIVE_INFO);
            assert($earthQuakeInfo instanceof CurlResponseInterface);

            if ($earthQuakeInfo->getStatus() !== 'success') {
                throw new Exception($earthQuakeInfo->getMessage() ?? 'Failed to get information');
            }

            if (mb_strlen(string: $earthQuakeInfo->getData('Magnitude')) < 1) {
                throw new Exception('Info data failed');
            }

            $hashedCache = CacheHelper::getCache(self::CACHE_INFO_NAME);

            $check = Hash::check(
                value: serialize($earthQuakeInfo->getData()),
                hashedValue: $hashedCache ?? $this->dummyHash,
            );

            if (! $check) {
                $this->notifyMessage = '_*NEW:*_ ';

                $postNotify = $this->notifyProcess($earthQuakeInfo);

                if ($postNotify) {
                    CacheHelper::setCache(
                        cacheName: self::CACHE_INFO_NAME,
                        value: Hash::make(serialize($earthQuakeInfo->getData())),
                    );
                }
            }

            $this->setResponseData(message: 'Earth quake notification is done');

            return $this->serviceResult(status: true, message: 'Earth quake notification is done');
        } catch (Throwable $th) {
            // $this->log($th);
            $this->setResponseData(message: $th->getMessage(), httpCode: 202);

            return $this->serviceResult(message: $th->getMessage());
        }
    }

    /**
     * Get un-sent information(s)
     *
     * @return array
     */
    public function getUnsentInformation(): array
    {
        try {
            $input = app(QuerySearchInput::class);
            assert($input instanceof QuerySearchInputInterface);

            $input->setCustomBuilder(EarthQuake::getUnsent());

            foreach (PaginatorParam::getResultSortOptions() ?? [] as $attribute => $type) {
                $input->addOrderConditions(
                    column: $attribute,
                    direction: @$type,
                );
            }

            $input->setPerPage(PaginatorParam::getPerPage());
            $input->setCurrentPage(PaginatorParam::getCurrentPage());

            $search = $this->earthQuakeRepository->search($input);

            $resultPaginate = $search->getPaginate();
            $resultPaginate->setDataSort(PaginatorParam::getResultSortOptions(asMultiple: true));

            $result = $resultPaginate->toArray();

            $this->setResponseData(message: 'List un-sent earth quake information', data: $result);

            return $this->serviceResult(status: true, message: 'List un-sent earth quake information', data: $result);
        } catch (Throwable $th) {
            $this->log($th);
            $this->setResponseData(message: $th->getMessage(), status: 'error', httpCode: 202);

            return $this->serviceResult(message: $th->getMessage());
        }
    }

    /**
     * Post notify un-sent information
     *
     * @return array
     */
    public function notifyUnsentInformation(): array
    {
        try {
            /** @var Collection<EarthQuakeInterface> $earthQuakeCollection */
            $earthQuakeCollection = $this->earthQuakeRepository->getUnsentOnly();

            $unsentCount = $earthQuakeCollection->count();
            assert(is_int($unsentCount));

            $postSuccess = 0;
            assert(is_int($postSuccess));

            foreach ($earthQuakeCollection?->all() ?? [] as $key => &$earthQuakeEntity) {
                assert($earthQuakeEntity instanceof EarthQuakeInterface);
                $this->notifyMessage = '_*RE-INFO:*_ ';

                $postMessage = $this->notifyProcess(
                    new CurlResponse([
                        ResponseInterface::ATTRIBUTE_MESSAGE => 'success',
                        ResponseInterface::ATTRIBUTE_DATA => unserialize($earthQuakeEntity->getBody()),
                    ]),
                );

                if (! $postMessage) {
                    continue;
                }

                $postSuccess++;
            }

            $result = [];

            $message = sprintf('SUccessfully post notify %s from %s information', $postSuccess, $unsentCount);

            $this->setResponseData(message: $message, data: $result);

            return $this->serviceResult(status: true, message: $message, data: $result);
        } catch (Throwable $th) {
            // $this->log($th);
            $this->setResponseData(message: $th->getMessage(), status: 'error', httpCode: 202);

            return $this->serviceResult(message: $th->getMessage());
        }
    }

    // ? Protected Methods

    /**
     * Notify process with body from curl information
     */
    protected function notifyProcess(CurlResponseInterface $curlResponseInterface): bool
    {
        try {
            $sendMessage = $this->sendMessage($curlResponseInterface);

            $earthQuakeEntity = $this->earthQuakeRepository->getByBody(
                pieceOfBody: explode(separator: '.', string: $curlResponseInterface->getData('Shakemap'))[0],
            );
            assert($earthQuakeEntity instanceof EarthQuakeInterface || $earthQuakeEntity === null);

            if (! $earthQuakeEntity?->getId()) {
                $earthQuakeEntity = new EarthQuake();
                assert($earthQuakeEntity instanceof EarthQuakeInterface);
            }

            $earthQuakeEntity->setBody(serialize($curlResponseInterface->getData()));
            $earthQuakeEntity->setSent(true);

            if (! $sendMessage) {
                $earthQuakeEntity->setSent(false);

                $this->earthQuakeRepository->createOrUpdate($earthQuakeEntity);

                throw new Exception('Failed to send message');
            }

            $this->earthQuakeRepository->createOrUpdate($earthQuakeEntity);

            return true;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * Send whatsapp message
     *
     * @throws Throwable
     */
    protected function sendMessage(CurlResponseInterface $curlResponseInterface): bool
    {
        try {
            if ($this->useContacts) {
                $this->setNotifyIndividuals(
                    notifyIndividuals: array_merge(
                        $this->getNotifyIndividuals(),
                        $this->getContactList(type: ContactInterface::TYPE_GROUP),
                    ),
                );

                $this->setNotifyGroups(
                    notifyGroups: array_merge(
                        $this->getNotifyGroups(),
                        $this->getContactList(type: ContactInterface::TYPE_PERSON),
                    ),
                );
            }

            $process = $this->whatsappMessageService
                ->setPersonIds($this->getNotifyIndividuals())
                ->setGroupIds($this->getNotifyGroups())
                ->setMessage($this->bodyMessage($curlResponseInterface))
                ->sendMessage();

            if (! $process['status']) {
                throw new Exception($process['message']);
            }

            return true;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * Custom body message for notification
     */
    protected function bodyMessage(CurlResponseInterface $curlResponseInterface): string
    {
        if (mb_strlen(string: $curlResponseInterface->getData('Magnitude')) < 1) {
            throw new Exception('Curl failed');
        }

        $this->notifyMessage .= sprintf(
            'Gempa *M %s*\n_*%s*_\nTanggal: *%s*\nPukul: *%s*\nPeta Guncangan: %s\nSumber: %s',
            $curlResponseInterface->getData('Magnitude'),
            $curlResponseInterface->getData('Wilayah'),
            $curlResponseInterface->getData('Tanggal'),
            $curlResponseInterface->getData('Jam'),
            sprintf('%s/%s', BmkgConfigInterface::BASE_URL_STATIC, $curlResponseInterface->getData('Shakemap')),
            BmkgConfigInterface::BASE_URL_WARNING,
        );

        return $this->notifyMessage;
    }

    /**
     * Get contact list
     *
     * @return array
     */
    protected function getContactList(string $type, bool $canBeNotified = true): array
    {
        return array_map(
            callback: static fn (array $contact): string => $contact[ContactInterface::ATTRIBUTE_IDENTITY],
            array: $this->contactRepository->throwIfNullEntity(false)->getList(onlyCanBeNotify: $canBeNotified, onlyType: $type)->toArray(),
        );
    }

    // ? Private Methods

    // ? Getter Modules

    /**
     * Get notify groups
     */
    public function getNotifyGroups(): array
    {
        return collect($this->notifyGroups)->filter()->toArray();
    }

    /**
     * Get notify individuals
     */
    public function getNotifyIndividuals(): array
    {
        return collect($this->notifyIndividuals)->filter()->toArray();
    }

    // ? Setter Modules

    /**
     * Add notify group
     */
    public function addNotifyGroup(string $group): self
    {
        $this->notifyGroups[] = $group;

        return $this;
    }

    /**
     * Set notify groups
     */
    public function setNotifyGroups(array $notifyGroups): self
    {
        $this->notifyGroups = $notifyGroups;

        return $this;
    }

    /**
     * Add notify individual
     */
    public function addNotifyIndividual(string $individual): self
    {
        $this->notifyIndividuals[] = $individual;

        return $this;
    }

    /**
     * Set notify individuals
     */
    public function setNotifyIndividuals(array $notifyIndividuals): self
    {
        $this->notifyIndividuals = $notifyIndividuals;

        return $this;
    }

    /**
     * Use contact from config
     */
    public function useContacts(bool $useContacts = false): self
    {
        $this->useContacts = $useContacts;

        return $this;
    }
}
