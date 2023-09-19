<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Traits\Models;

use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;
use TheBachtiarz\Party\Contact\Models\Contact;

use function array_merge;
use function array_unique;

/**
 * Contact Map Trait
 */
trait ContactMapTrait
{
    /**
     * Contact simple list map
     *
     * @param array $attributes
     *
     * @return array
     */
    public function simpleListMap(array $attributes = []): array
    {
        /** @var Contact $this */

        $returnAttributes = [
            ContactInterface::ATTRIBUTE_IDENTITY,
            ContactInterface::ATTRIBUTE_TYPE,
            ContactInterface::ATTRIBUTE_NOTIFY,
        ];

        $this->makeHidden([
            ContactInterface::ATTRIBUTE_ID,
            ContactInterface::ATTRIBUTE_CREATEDAT,
            ContactInterface::ATTRIBUTE_UPDATEDAT,
        ]);

        return $this->only(attributes: array_unique(array_merge($returnAttributes, $attributes)));
    }
}
