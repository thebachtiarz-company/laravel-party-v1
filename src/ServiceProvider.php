<?php

declare(strict_types=1);

namespace TheBachtiarz\Party;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

use function app;
use function assert;
use function config_path;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $appService = app(AppService::class);
        assert($appService instanceof AppService);

        $appService->registerConfig();

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands(AppService::COMMANDS);
    }

    /**
     * Boot
     */
    public function boot(): void
    {
        if (! app()->runningInConsole()) {
            return;
        }

        $configName  = PartyConfigInterface::CONFIG_NAME;
        $publishName = 'thebachtiarz-party';

        $this->publishes([__DIR__ . "/../config/$configName.php" => config_path("$configName.php")], "$publishName-config");
        // $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], "$publishName-migrations");
    }
}
