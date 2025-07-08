<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Handlers;

use MXRVX\Autoloader\App;
use MXRVX\Autoloader\PackageManager;

/**
 * @psalm-import-type ArrayNamespaceStructure from PackageManager
 */
class AutoloaderHandler
{
    public function __construct(protected App $app, protected PackageManager $packageManager) {}

    /**
     * @return array<string, string>
     */
    public function getAutoloaders(): array
    {
        [$modx, ] = $this->app->getReferences();

        $componentPath = MODX_CORE_PATH . 'components/';
        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $modx->call(\modNamespace::class, 'loadCache', [$modx]);
        $namespacesAutoloader = $this->packageManager->getNamespacesAutoloader($componentPath, $namespaces);

        $packagesAutoloader = $this->packageManager->getPackagesAutoloader($componentPath);

        return \array_intersect_key($namespacesAutoloader, $packagesAutoloader);
    }

    public function __invoke(): void
    {
        [$modx, $container] = $this->app->getReferences();

        $showErrors = $this->app->config->getSetting('show_errors')?->getBoolValue();

        $autoloaders = $this->getAutoloaders();
        foreach ($autoloaders as $autoloader) {
            if (\file_exists($autoloader)) {

                try {
                    require $autoloader;

                    $autoloaders[$autoloader] = true;

                } catch (\Throwable $e) {
                    $autoloaders[$autoloader] = false;

                    if ($showErrors) {
                        $this->app->log(
                            \sprintf(
                                'include `%s` failed with an error: `%s` line: `%s`',
                                $e->getFile(),
                                $e->getMessage(),
                                $e->getLine(),
                            ),
                        );
                    }
                }
            } else {
                $autoloaders[$autoloader] = null;
            }
        }


        if ($this->app->config->getSetting('show_loads')?->getBoolValue()) {
            $this->app->log(
                \sprintf(
                    'loaded `%s` of autoloaders `%s`',
                    \count($autoloaders),
                    \var_export($autoloaders, true),
                ),
            );
        }
    }
}
