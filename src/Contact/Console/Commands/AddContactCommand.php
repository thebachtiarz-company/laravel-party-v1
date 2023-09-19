<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Contact\Console\Commands;

use TheBachtiarz\Base\App\Console\Commands\AbstractCommand;
use TheBachtiarz\Party\Contact\Services\ContactService;

class AddContactCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct(
        protected ContactService $contactService,
    ) {
        $this->signature    = 'party:contact:add
                                {--identity= : Contact identity}
                                {--type=2 : Contact type (1 => Group | 2 => Person)}
                                {--notify=1 : Contact can be notify (1 => Can | 2 => Cannot)}';
        $this->commandTitle = 'Add Contact';
        $this->description  = 'Add contact information';

        parent::__construct();
    }

    // ? Public Methods

    public function commandProcess(): bool
    {
        $process = $this->contactService->addContact(
            identity: $this->option('identity'),
            type: $this->option('type'),
            notify: $this->option('notify') === '1',
        );

        return $process['status'];
    }

    // ? Protected Methods

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
