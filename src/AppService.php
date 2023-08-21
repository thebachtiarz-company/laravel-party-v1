<?php

declare(strict_types=1);

namespace TheBachtiarz\Party;

use TheBachtiarz\Party\Bmkg\Console\Commands\EarthQuakeNotifyCommand;

use function app;
use function assert;
use function collect;
use function config;

class AppService
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Available command modules
     */
    public const COMMANDS = [EarthQuakeNotifyCommand::class];

    // ? Public Methods

    /**
     * Register config
     */
    public function registerConfig(): void
    {
        $this->setConfigs();
    }

    // ? Private Methods

    /**
     * Set configs
     */
    private function setConfigs(): void
    {
        $dataService = app(DataService::class);
        assert($dataService instanceof DataService);

        foreach ($dataService->registerConfig() as $key => $register) {
            config(collect($register)->unique()->toArray());
        }
    }
}
