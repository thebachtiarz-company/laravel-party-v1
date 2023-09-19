<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TheBachtiarz\Base\App\Repositories\AbstractRepository;
use TheBachtiarz\Party\Bmkg\Interfaces\Models\EarthQuakeInterface;
use TheBachtiarz\Party\Bmkg\Models\EarthQuake;

use function app;
use function assert;

class EarthQuakeRepository extends AbstractRepository
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelEntity = app(EarthQuake::class);

        parent::__construct();
    }

    // ? Public Methods

    /**
     * Get by piece of body
     */
    public function getByBody(string $pieceOfBody): EarthQuakeInterface|null
    {
        $this->modelBuilder(modelBuilder: EarthQuake::getByBody($pieceOfBody));

        $entity = $this->modelBuilder()->first();
        assert($entity instanceof EarthQuakeInterface || $entity === null);

        return $entity;
    }

    /**
     * Get un-sent information
     *
     * @return Collection<EarthQuakeInterface>
     */
    public function getUnsentOnly(): Collection
    {
        $this->modelBuilder(modelBuilder: EarthQuake::getUnsent());

        if (! $this->modelBuilder()->count() && $this->throwIfNullEntity()) {
            throw new ModelNotFoundException('There is no any unsent information');
        }

        return $this->modelBuilder()->get();
    }

    // ? Protected Methods

    protected function getByIdErrorMessage(): string|null
    {
        return "Earth quake information with id '%s' not found";
    }

    protected function createOrUpdateErrorMessage(): string|null
    {
        return 'Failed to %s earth quake information';
    }

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
