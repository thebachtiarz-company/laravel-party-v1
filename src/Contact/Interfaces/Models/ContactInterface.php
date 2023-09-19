<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Interfaces\Models;

use TheBachtiarz\Base\App\Interfaces\Models\AbstractModelInterface;

interface ContactInterface extends AbstractModelInterface
{
    /**
     * Table name
     */
    public const TABLE_NAME = 'contacts';

    /**
     * Model attributes
     */
    public const ATTRIBUTE_FILLABLE = [
        self::ATTRIBUTE_IDENTITY,
        self::ATTRIBUTE_TYPE,
        self::ATTRIBUTE_NOTIFY,
    ];

    public const ATTRIBUTE_IDENTITY = 'identity';
    public const ATTRIBUTE_TYPE     = 'type';
    public const ATTRIBUTE_NOTIFY   = 'notify';

    public const TYPE_GROUP  = '1';
    public const TYPE_PERSON = '2';

    // ? Getter Modules

    /**
     * Get identity
     */
    public function getIdentity(): string;

    /**
     * Get type
     */
    public function getType(): string;

    /**
     * Get notify
     */
    public function getNotify(): bool;

    // ? Setter Modules

    /**
     * Set identity
     */
    public function setIdentity(string $identity): self;

    /**
     * Set type
     */
    public function setType(string $type): self;

    /**
     * Set notify
     */
    public function setNotify(bool $notify = false): self;
}
