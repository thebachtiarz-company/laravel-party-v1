<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Models;

use TheBachtiarz\Base\App\Models\AbstractModel;
use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;
use TheBachtiarz\Party\Contact\Traits\Models\ContactMapTrait;
use TheBachtiarz\Party\Contact\Traits\Models\ContactScopeTrait;

class Contact extends AbstractModel implements ContactInterface
{
    use ContactMapTrait;
    use ContactScopeTrait;

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
     * Get identity
     */
    public function getIdentity(): string
    {
        return $this->__get(self::ATTRIBUTE_IDENTITY);
    }

    /**
     * Get type
     */
    public function getType(): string
    {
        return $this->__get(self::ATTRIBUTE_TYPE);
    }

    /**
     * Get notify
     */
    public function getNotify(): bool
    {
        return $this->__get(self::ATTRIBUTE_NOTIFY);
    }

    // ? Setter Modules

    /**
     * Set identity
     */
    public function setIdentity(string $identity): self
    {
        $this->__set(self::ATTRIBUTE_IDENTITY, $identity);

        return $this;
    }

    /**
     * Set type
     */
    public function setType(string $type): self
    {
        $this->__set(self::ATTRIBUTE_TYPE, $type);

        return $this;
    }

    /**
     * Set notify
     */
    public function setNotify(bool $notify = false): self
    {
        $this->__set(self::ATTRIBUTE_NOTIFY, $notify);

        return $this;
    }
}
