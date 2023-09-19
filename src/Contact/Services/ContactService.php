<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Services;

use TheBachtiarz\Base\App\Services\AbstractService;
use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;
use TheBachtiarz\Party\Contact\Models\Contact;
use TheBachtiarz\Party\Contact\Repositories\ContactRepository;
use Throwable;

use function assert;

class ContactService extends AbstractService
{
    /**
     * Constructor
     */
    public function __construct(
        protected ContactRepository $contactRepository,
    ) {
    }

    // ? Public Methods

    /**
     * Add new contact
     *
     * @return array
     */
    public function addContact(
        string $identity,
        string|null $type = ContactInterface::TYPE_PERSON,
        bool|null $notify = true,
    ): array {
        try {
            $contactEntity = $this->contactRepository->throwIfNullEntity(false)->getByIdentity($identity);
            assert($contactEntity instanceof ContactInterface || $contactEntity === null);

            if (! $contactEntity?->getId()) {
                $contactEntity = new Contact();
            }

            $contactEntity->setIdentity($identity);
            $contactEntity->setType(match ($type) {
                ContactInterface::TYPE_GROUP => ContactInterface::TYPE_GROUP,
                ContactInterface::TYPE_PERSON => ContactInterface::TYPE_PERSON,
                default => ContactInterface::TYPE_PERSON,
            });
            $contactEntity->setNotify($notify);

            $process = $this->contactRepository->createOrUpdate($contactEntity);
            assert($process instanceof ContactInterface);

            $result = $process->simpleListMap();

            $this->setResponseData(message: 'Successfully add new contact', data: $result);

            return $this->serviceResult(status: true, message: 'Successfully add new contact', data: $result);
        } catch (Throwable $th) {
            $this->log($th);
            $this->setResponseData(message: $th->getMessage(), status: 'error', httpCode: 202);

            return $this->serviceResult(message: $th->getMessage());
        }
    }

    // ? Protected Methods

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
