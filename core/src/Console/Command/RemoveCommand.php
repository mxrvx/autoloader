<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MXRVX\Autoloader\App;

class RemoveCommand extends Command
{
    protected static $defaultName = 'remove';
    protected static $defaultDescription = 'Remove "' . App::NAMESPACE . '" extra from MODX';

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $modx = $this->app->getModx();

        $corePath = MODX_CORE_PATH . 'components/' . App::NAMESPACE;
        if (\is_dir($corePath)) {
            \unlink($corePath);
            $output->writeln('<info>Removed symlink for `core`</info>');
        }

        if ($namespace = $modx->getObject(\modNamespace::class, ['name' => App::AUTOLOADER])) {
            $namespace->remove();
            $output->writeln(\sprintf('<info>Removed namespace `%s`</info>', App::NAMESPACE));
        }

        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }
}
