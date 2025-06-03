<?php

declare(strict_types=1);

namespace MXRVX\Autoloader;

use MXRVX\Schema\System\Settings\SchemaConfigInterface;

/**
 * @psalm-import-type ArrayNamespaceStructure from Manager
 */
class App
{
    public const NAMESPACE = 'mxrvx-autoloader';
    public const AUTOLOADER = '_autoloader';

    public SchemaConfigInterface $config;
    private Manager $manager;
    private bool $bootstrapAutoloadProcessed = false;
    private static bool $connectorRequestProcessed = false;

    public function __construct(private \xPDO $xpdo)
    {
        \Composer\InstalledVersions::getAllRawData();

        $this->config = Config::make($xpdo->config);
        $this->manager = Manager::create(MODX_BASE_PATH . 'composer.lock', \dirname(__DIR__) . '/packages');

        if (!$this->manager->hasValidLock()) {
            $this->log(
                \sprintf(
                    '`%s` is not valid composer.lock file',
                    $this->manager->getLockFile(),
                ),
            );
        } else {
            if ((bool) $this->config->getSetting('active')?->getValue()) {
                $this->processBootstrapAutoload();
                $this->processConnectorRequest();
            }
        }
    }

    public function xpdo(): \xPDO
    {
        return $this->xpdo;
    }

    public function manager(): Manager
    {
        return $this->manager;
    }

    public function log(string $message): void
    {
        if (\method_exists($this->xpdo, 'log')) {
            $this->xpdo->log(\xPDO::LOG_LEVEL_ERROR, $message);
        }
    }

    protected function processBootstrapAutoload(): void
    {
        if ($this->bootstrapAutoloadProcessed) {
            return;
        }
        $this->bootstrapAutoloadProcessed = true;

        $xpdo = $this->xpdo();
        $showErrors = (bool) $this->config->getSetting('show_errors')?->getValue();

        $componentPath = MODX_CORE_PATH . 'components/';
        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $xpdo->call(\modNamespace::class, 'loadCache', [&$xpdo]);
        $namespacesBootstrap = $this->manager->getNamespacesBootstrap($componentPath, $namespaces);
        $packagesBootstrap = $this->manager->getPackagesBootstrap($componentPath);
        $loadableBootstrap = \array_intersect_key($namespacesBootstrap, $packagesBootstrap);
        foreach ($loadableBootstrap as $bootstrap) {
            $loadableBootstrap[$bootstrap] = $this->loadBootstrap($bootstrap, $showErrors);
        }

        if ((bool) $this->config->getSetting('show_loads')?->getValue()) {
            $this->log(
                \sprintf(
                    'loaded `%s` of `%s`',
                    \count($loadableBootstrap),
                    \var_export($loadableBootstrap, true),
                ),
            );
        }
    }

    protected function processConnectorRequest(): void
    {
        if (self::$connectorRequestProcessed) {
            return;
        }
        self::$connectorRequestProcessed = true;

        $xpdo = $this->xpdo();
        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $xpdo->call(\modNamespace::class, 'loadCache', [&$xpdo]);

        $matches = [];
        if (!empty($_SERVER['REQUEST_URI']) && \preg_match('#^/assets/components/([^/]+)/api/([^/.]+)/(.*)?#', $_SERVER['REQUEST_URI'], $matches)) {
            $namespace = $matches[1];
            $connector = $matches[2];
            if (isset($namespaces[$namespace])) {
                $connectorPath = \sprintf(
                    '%s/assets/components/%s/api/%s.php',
                    $_SERVER['DOCUMENT_ROOT'] ?? '',
                    $namespace,
                    $connector,
                );
                if (\file_exists($connectorPath)) {
                    require $connectorPath;
                    exit;
                }
            }
        }

    }

    protected function loadBootstrap(string $file, bool $showErrors = false): bool
    {
        if (!\file_exists($file)) {
            return false;
        }

        try {
            require_once $file;
            return true;
        } catch (\Throwable $e) {
            if ($showErrors) {
                $this->log(
                    \sprintf(
                        'include `%s` failed with an error: `%s` line: `%s`',
                        $e->getFile(),
                        $e->getMessage(),
                        $e->getLine(),
                    ),
                );
            }
        }

        return false;
    }
}
