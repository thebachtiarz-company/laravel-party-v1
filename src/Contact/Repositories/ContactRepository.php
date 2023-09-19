<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TheBachtiarz\Base\App\Repositories\AbstractRepository;
use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;
use TheBachtiarz\Party\Contact\Models\Contact;

use function app;
use function assert;

class ContactRepository extends AbstractRepository
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelEntity = app(Contact::class);

        parent::__construct();
    }

    // ? Public Methods

    /**
     * Get by identity
     */
    public function getByIdentity(string $identity): ContactInterface|null
    {
        $this->modelBuilder(modelBuilder: Contact::getByIdentity($identity));

        $entity = $this->modelBuilder()->first();
        assert($entity instanceof ContactInterface || $entity === null);

        if (! $entity && $this->throwIfNullEntity()) {
            throw new ModelNotFoundException("Contact with identity '$identity' not found");
        }

        return $entity;
    }

    /**
     * Get contact list
     */
    public function getList(bool $onlyCanBeNotify = false, string|null $onlyType = null): Collection
    {
        $modelBuilder = $onlyCanBeNotify ? Contact::onlyCanBeNotify() : Contact::query();

        if ($onlyType) {
            $modelBuilder->onlyType($onlyType);
        }

        $this->modelBuilder(modelBuilder: $modelBuilder);

        if (! $this->modelBuilder()->count() && $this->throwIfNullEntity()) {
            throw new ModelNotFoundException('Contact list is empty');
        }

        return $this->modelBuilder()->get();
    }

    /**
     * Delete by identity
     */
    public function deleteByIdentity(string $identity): bool
    {
        $config = $this->throwIfNullEntity(false)->getByIdentity($identity);

        if (! $config) {
            throw new ModelNotFoundException('Failed to delete contact');
        }

        return $this->deleteById($config->getId());
    }

    // ? Protected Methods

    protected function getByIdErrorMessage(): string|null
    {
        return "Contact with id '%s' not found";
    }

    protected function createOrUpdateErrorMessage(): string|null
    {
        return 'Failed to %s Contact';
    }

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
