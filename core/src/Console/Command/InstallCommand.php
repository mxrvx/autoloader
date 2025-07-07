<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MXRVX\Autoloader\App;

class InstallCommand extends Command
{
    protected static $defaultName = 'install';
    protected static $defaultDescription = 'Install "' . App::NAMESPACE . '" extra for MODX';

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $app = $this->app;
        $modx = $this->app->getModx();

        $srcPath = MODX_CORE_PATH . 'vendor/' . \str_replace('-', '/', App::NAMESPACE);
        $corePath = MODX_CORE_PATH . 'components/' . App::NAMESPACE;

        if (!\is_dir($corePath)) {
            \symlink($srcPath . '/core', $corePath);
            $output->writeln('<info>Created symlink for `core`</info>');
        }

        if (!$modx->getObject(\modNamespace::class, ['name' => App::AUTOLOADER])) {
            /** @var \modNamespace $namespace */
            $namespace = $modx->newObject(\modNamespace::class);
            $namespace->fromArray(
                [
                    'name' => App::AUTOLOADER,
                    'path' => '{core_path}components/' . App::NAMESPACE . '/',
                    'assets_path' => '',
                ],
                '',
                true,
            );
            $namespace->save();
            $output->writeln(\sprintf('<info>Created namespace `%s`</info>', App::NAMESPACE));
        }

        if (!$modx->getObject(\modExtensionPackage::class, ['name' => App::NAMESPACE])) {
            /** @var \modExtensionPackage $extension */
            $extension = $modx->newObject(\modExtensionPackage::class);
            $extension->fromArray(
                [
                    'name' => App::NAMESPACE,
                    'namespace' => App::AUTOLOADER,
                    'service_name' => App::AUTOLOADER,
                    'service_class' => App::AUTOLOADER,
                    'path' => MODX_CORE_PATH . 'components/' . App::NAMESPACE . '/',
                ],
                '',
                true,
            );
            $extension->save();
            $output->writeln(\sprintf('<info>Created extension package `%s`</info>', App::NAMESPACE));
        }

        /** @var array{key: string, value: mixed} $row */
        foreach ($app->config->getSettingsArray() as $row) {
            if (!$modx->getObject(\modSystemSetting::class, $row['key'])) {
                /** @var \modSystemSetting $setting */
                $setting = $modx->newObject(\modSystemSetting::class);
                $setting->fromArray($row, '', true);
                $setting->save();
                $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $row['key']));
            }
        }

        $modx->getCacheManager()->refresh();

        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }
}
