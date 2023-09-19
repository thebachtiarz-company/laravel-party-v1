<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Traits\Models;

use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;

/**
 * Contact Scope Trait
 */
trait ContactScopeTrait
{
    /**
     * Get by identity
     */
    public function scopeGetByIdentity(EloquentBuilder|QueryBuilder $builder, string $identity): BuilderContract
    {
        $attribute = ContactInterface::ATTRIBUTE_IDENTITY;

        return $builder->where(
            column: DB::raw("BINARY `$attribute`"),
            operator: '=',
            value: $identity,
        );
    }

    /**
     * Only type
     */
    public function scopeOnlyType(EloquentBuilder|QueryBuilder $builder, string $type): BuilderContract
    {
        return $builder->where(
            column: ContactInterface::ATTRIBUTE_TYPE,
            operator: '=',
            value: match ($type) {
                ContactInterface::TYPE_GROUP => ContactInterface::TYPE_GROUP,
                ContactInterface::TYPE_PERSON => ContactInterface::TYPE_PERSON,
                default => ContactInterface::TYPE_PERSON,
            },
        );
    }

    /**
     * Only can be notify
     */
    public function scopeOnlyCanBeNotify(EloquentBuilder|QueryBuilder $builder): BuilderContract
    {
        return $builder->where(
            column: ContactInterface::ATTRIBUTE_NOTIFY,
            operator: '=',
            value: true,
        );
    }
}
