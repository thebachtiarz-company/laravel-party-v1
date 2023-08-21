<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Services;

use Illuminate\Support\Facades\Hash;
use TheBachtiarz\Base\App\Helpers\CacheHelper;
use TheBachtiarz\Base\App\Libraries\Curl\Data\CurlResponseInterface;
use TheBachtiarz\Base\App\Services\AbstractService;
use TheBachtiarz\Party\Bmkg\Libraries\CurlBmkgLibrary;
use TheBachtiarz\Party\Bmkg\Interfaces\Configs\BmkgConfigInterface;
use TheBachtiarz\WhatsApp\Services\WhatsAppMessageService;
use Throwable;

use function json_encode;
use function sprintf;

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
     * Constructor
     */
    public function __construct(
        protected CurlBmkgLibrary $curlBmkgLibrary,
        protected WhatsAppMessageService $whatsappMessageService,
    ) {
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
            $process = $this->curlBmkgLibrary->execute(CurlBmkgLibrary::EARTHQUAKE_LIVE_INFO);

            $hashed = CacheHelper::getCache(self::CACHE_INFO_NAME);

            if (! $hashed) {
                CacheHelper::setCache(
                    cacheName: self::CACHE_INFO_NAME,
                    value: Hash::make(json_encode($process->getData())),
                );
            }

            $check = Hash::check(
                value: json_encode($process->getData()),
                hashedValue: $hashed ?? '$2y$10$lJpBtAuHr9JKedWtic3wN.sWZP1T1pZkUYiXsFgtgawl39bg1m5Y6',
            );

            if (! $check) {
                // Update local data

                CacheHelper::setCache(
                    cacheName: self::CACHE_INFO_NAME,
                    value: Hash::make(json_encode($process->getData())),
                );

                // Send to chat apps api

                $this->sendMessage($process);
            }

            $this->setResponseData(message: 'Notify OK');

            return $this->serviceResult(status: true, message: 'Notify OK');
        } catch (Throwable $th) {
            // $this->log($th);
            $this->setResponseData(message: $th->getMessage(), httpCode: 202);

            return $this->serviceResult(message: $th->getMessage());
        }
    }

    // ? Protected Methods

    /**
     * Send whatsapp message
     */
    protected function sendMessage(CurlResponseInterface $curlResponseInterface): void
    {
        $this->whatsappMessageService
            ->setPersonIds($this->getNotifyIndividuals())
            ->setGroupIds($this->getNotifyGroups())
            ->setMessage($this->bodyMessage($curlResponseInterface))
            ->sendMessage();
    }

    /**
     * Custom body message for notification
     */
    protected function bodyMessage(CurlResponseInterface $curlResponseInterface): string
    {
        return sprintf(
            'Gempa *M %s*\n_*%s*_\nTanggal: *%s*\nPukul: *%s*\nPeta Guncangan: %s\nSumber: %s',
            $curlResponseInterface->getData('Magnitude'),
            $curlResponseInterface->getData('Wilayah'),
            $curlResponseInterface->getData('Tanggal'),
            $curlResponseInterface->getData('Jam'),
            sprintf('%s/%s', BmkgConfigInterface::BASE_URL_STATIC, $curlResponseInterface->getData('Shakemap')),
            BmkgConfigInterface::BASE_URL_WARNING,
        );
    }

    // ? Private Methods

    // ? Getter Modules

    /**
     * Get notify groups
     */
    public function getNotifyGroups(): array
    {
        return $this->notifyGroups;
    }

    /**
     * Get notify individuals
     */
    public function getNotifyIndividuals(): array
    {
        return $this->notifyIndividuals;
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
}
