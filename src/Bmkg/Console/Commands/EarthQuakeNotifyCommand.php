<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Console\Commands;

use TheBachtiarz\Base\App\Console\Commands\AbstractCommand;
use TheBachtiarz\Party\Bmkg\Services\EarthQuakeService;

use function explode;

class EarthQuakeNotifyCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct(
        protected EarthQuakeService $earthQuakeService,
    ) {
        $this->signature    = 'party:bmkg:notify:earthquake
                                {--groupIds= : Group(s) ID (multiple with comma)}
                                {--personIds= : Person(s) ID (multiple with comma)}
                                {--useContacts=2 : Use list contact(s) from config (1 => Yes | 2 => No)}';
        $this->commandTitle = 'Send Earthquake Notification';
        $this->description  = 'Send earthquake notification to message apps';

        parent::__construct();
    }

    // ? Public Methods

    public function commandProcess(): bool
    {
        $prepare = $this->earthQuakeService;

        $prepare->setNotifyIndividuals(explode(separator: ',', string: $this->option('personIds') ?? ''));
        $prepare->setNotifyGroups(explode(separator: ',', string: $this->option('groupIds') ?? ''));
        $prepare->useContacts($this->option('useContacts') === '1');

        $process = $prepare->pushNotify();

        return $process['status'];
    }

    // ? Protected Methods

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
