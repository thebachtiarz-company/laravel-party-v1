<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Libraries;

use TheBachtiarz\Base\App\Libraries\Curl\CurlLibrary;
use TheBachtiarz\Party\Bmkg\Libraries\Live\EarthQuakeInfo;

class CurlBmkgLibrary extends CurlLibrary
{
    public const EARTHQUAKE_LIVE_INFO = 'earthquake-live-info';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classEntity = [
            self::EARTHQUAKE_LIVE_INFO => EarthQuakeInfo::class,
        ];
    }

    // ? Public Methods

    // ? Protected Methods

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
