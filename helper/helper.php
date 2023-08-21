<?php

declare(strict_types=1);

use TheBachtiarz\Party\PartyConfigInterface;

if (! function_exists('tbpartyconfig')) {
    /**
     * TheBachtiarz party config
     *
     * @param string|null $keyName   Config key name | null will return all
     * @param bool|null   $useOrigin Use original value from config
     */
    function tbpartyconfig(string|null $keyName = null, bool|null $useOrigin = true): mixed
    {
        $configName = PartyConfigInterface::CONFIG_NAME;

        return tbconfig($configName, $keyName, $useOrigin);
    }
}
