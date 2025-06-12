<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Console;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use MXRVX\Autoloader\App;
use MXRVX\Autoloader\Console\Command\InstallCommand;
use MXRVX\Autoloader\Console\Command\RemoveCommand;

class Console extends Application
{
    public function __construct(protected Container $container)
    {
        parent::__construct(App::NAMESPACE);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function getDefaultCommands(): array
    {
        return [
            new ListCommand(),
            new InstallCommand($this->container),
            new RemoveCommand($this->container),
        ];
    }
}
