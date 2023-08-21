<?php

declare(strict_types=1);

namespace TheBachtiarz\Bmkg\Console\Commands;

use TheBachtiarz\Base\App\Console\Commands\AbstractCommand;
use TheBachtiarz\Bmkg\Services\EarthQuakeService;

use function explode;

class EarthQuakeNotifyCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct(
        protected EarthQuakeService $earthQuakeService,
    ) {
        $this->signature    = 'bmkg:notify:earthquake
                                {--groupIds= : Group(s) ID (multiple with comma)}
                                {--personIds= : Person(s) ID (multiple with comma)}';
        $this->commandTitle = 'Send earthquake notification';
        $this->description  = 'Send earthquake notification to message apps';

        parent::__construct();
    }

    // ? Public Methods

    public function commandProcess(): bool
    {
        $prepare = $this->earthQuakeService;
        $prepare->setNotifyIndividuals(explode(separator: ',', string: $this->option('personIds') ?? ''));
        $prepare->setNotifyGroups(explode(separator: ',', string: $this->option('groupIds') ?? ''));

        $process = $prepare->pushNotify();

        return $process['status'];
    }

    // ? Protected Methods

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
