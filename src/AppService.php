<?php

namespace TheBachtiarz\Party;

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
    public const COMMANDS = [];

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
