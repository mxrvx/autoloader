<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Console\Command;

use MXRVX\Autoloader\App;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    public const SUCCESS = SymfonyCommand::SUCCESS;
    public const FAILURE = SymfonyCommand::FAILURE;
    public const INVALID = SymfonyCommand::INVALID;

    public function __construct(protected App $app, ?string $name = null)
    {
        parent::__construct($name);
    }
}
