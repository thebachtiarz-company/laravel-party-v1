<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Models;

use TheBachtiarz\Base\App\Models\AbstractModel;
use TheBachtiarz\Party\Bmkg\Interfaces\Models\EarthQuakeInterface;
use TheBachtiarz\Party\Bmkg\Traits\Models\EarthQuakeMapTrait;
use TheBachtiarz\Party\Bmkg\Traits\Models\EarthQuakeScopeTrait;

class EarthQuake extends AbstractModel implements EarthQuakeInterface
{
    use EarthQuakeMapTrait;
    use EarthQuakeScopeTrait;

    /**
     * Constructor
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(self::TABLE_NAME);
        $this->fillable(self::ATTRIBUTE_FILLABLE);

        parent::__construct($attributes);
    }

    // ? Public Methods

    // ? Protected Methods

    // ? Private Methods

    // ? Getter Modules

    /**
     * Get body information
     */
    public function getBody(): string
    {
        return $this->__get(self::ATTRIBUTE_BODY);
    }

    /**
     * Get information send status
     */
    public function getSent(): bool
    {
        return $this->__get(self::ATTRIBUTE_SENT);
    }

    // ? Setter Modules

    /**
     * Set body information
     */
    public function setBody(string $body): self
    {
        $this->__set(self::ATTRIBUTE_BODY, $body);

        return $this;
    }

    /**
     * Set information send status
     */
    public function setSent(bool $sent = false): self
    {
        $this->__set(self::ATTRIBUTE_SENT, $sent);

        return $this;
    }
}
