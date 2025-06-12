<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Console\Command;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use MXRVX\Autoloader\App;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    public const SUCCESS = SymfonyCommand::SUCCESS;
    public const FAILURE = SymfonyCommand::FAILURE;
    public const INVALID = SymfonyCommand::INVALID;

    protected App $app;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(protected Container $container, ?string $name = null)
    {
        /** @var App $this->app */
        $this->app = $this->container->get(App::class);
        parent::__construct($name);
    }
}
