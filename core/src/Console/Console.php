<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use MXRVX\Autoloader\App;
use MXRVX\Autoloader\Console\Command\InstallCommand;
use MXRVX\Autoloader\Console\Command\RemoveCommand;

class Console extends Application
{
    public function __construct(protected App $app)
    {
        parent::__construct(App::NAMESPACE);
    }

    protected function getDefaultCommands(): array
    {
        return [
            new ListCommand(),
            new InstallCommand($this->app),
            new RemoveCommand($this->app),
        ];
    }
}
