<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Traits\Models;

use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use TheBachtiarz\Party\Bmkg\Interfaces\Models\EarthQuakeInterface;

/**
 * Earth Quake Scope Trait
 */
trait EarthQuakeScopeTrait
{
    public function scopeGetByBody(EloquentBuilder|QueryBuilder $builder, string $body): BuilderContract
    {
        return $builder->whereFullText(
            columns: EarthQuakeInterface::ATTRIBUTE_BODY,
            value: $body,
        );
    }

    /**
     * Get un-sent information
     */
    public function scopeGetUnsent(EloquentBuilder|QueryBuilder $builder): BuilderContract
    {
        return $builder->where(
            column: EarthQuakeInterface::ATTRIBUTE_SENT,
            operator: '=',
            value: false,
        );
    }
}
