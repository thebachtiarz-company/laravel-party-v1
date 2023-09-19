<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Traits\Models;

use TheBachtiarz\Party\Bmkg\Interfaces\Models\EarthQuakeInterface;
use TheBachtiarz\Party\Bmkg\Models\EarthQuake;

use function array_merge;
use function array_unique;
use function unserialize;

/**
 * Earth Quake Map Trait
 */
trait EarthQuakeMapTrait
{
    /**
     * Earth quake simple list map
     *
     * @param array $attributes
     *
     * @return array
     */
    public function simpleListMap(array $attributes = []): array
    {
        /** @var EarthQuake $this */

        $returnAttributes = [
            EarthQuakeInterface::ATTRIBUTE_BODY,
            EarthQuakeInterface::ATTRIBUTE_SENT,
        ];

        $this->makeHidden([
            EarthQuakeInterface::ATTRIBUTE_ID,
            EarthQuakeInterface::ATTRIBUTE_CREATEDAT,
            EarthQuakeInterface::ATTRIBUTE_UPDATEDAT,
        ]);

        $this->setData(
            attribute: EarthQuakeInterface::ATTRIBUTE_BODY,
            value: unserialize($this->getBody()),
        );

        return $this->only(attributes: array_unique(array_merge($returnAttributes, $attributes)));
    }
}
