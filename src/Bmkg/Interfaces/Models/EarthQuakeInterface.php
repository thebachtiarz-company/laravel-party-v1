<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Interfaces\Models;

use TheBachtiarz\Base\App\Interfaces\Models\AbstractModelInterface;

interface EarthQuakeInterface extends AbstractModelInterface
{
    /**
     * Table name
     */
    public const TABLE_NAME = 'earth_quakes';

    /**
     * Model attributes
     */
    public const ATTRIBUTE_FILLABLE = [
        self::ATTRIBUTE_BODY,
        self::ATTRIBUTE_SENT,
    ];

    public const ATTRIBUTE_BODY = 'body';
    public const ATTRIBUTE_SENT = 'sent';

    // ? Getter Modules

    /**
     * Get body information
     */
    public function getBody(): string;

    /**
     * Get information send status
     */
    public function getSent(): bool;

    // ? Setter Modules

    /**
     * Set body information
     */
    public function setBody(string $body): self;

    /**
     * Set information send status
     */
    public function setSent(bool $sent = false): self;
}
