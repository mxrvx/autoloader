<?php

declare(strict_types=1);

namespace MXRVX\Autoloader;

use DI\Container;
use DI\ContainerBuilder;
use MXRVX\Schema\System\Settings\SchemaConfigInterface;

/**
 * @psalm-import-type ArrayNamespaceStructure from Manager
 */
class App
{
    public const NAMESPACE = 'mxrvx-autoloader';
    public const AUTOLOADER = '_autoloader';

    public SchemaConfigInterface $config;
    protected Manager $manager;
    protected Container $container;

    /** @var static[] */
    protected static array $instances = [];

    /**
     * @throws \Exception
     */
    public function __construct(protected \modX $modx)
    {
        \Composer\InstalledVersions::getAllRawData();

        $this->manager = Manager::create(MODX_BASE_PATH . 'composer.lock', \dirname(__DIR__) . '/packages');

        $builder = new ContainerBuilder();
        $this->container = $builder->build();
        $this->container->set(\modX::class, $modx);

        $this->config = Config::make($modx->config);

        self::setInstance($this);

        $this->processBootstrapAutoload();
        $this->processConnectorRequest();

        if (!$this->manager->hasValidLock()) {
            $this->log(
                \sprintf(
                    '`%s` is not valid composer.lock file',
                    $this->manager->getLockFile(),
                ),
            );
        }
    }

    public static function getInstanceId(\modX $modx): int
    {
        return \spl_object_id($modx);
    }

    /**
     * @throws \Exception
     */
    public static function getInstance(\modX $modx): self
    {
        $id = self::getInstanceId($modx);
        if (!isset(self::$instances[$id])) {
            self::$instances[$id] = new self($modx);
        }
        return self::$instances[$id];
    }

    public static function setInstance(self $instance): void
    {
        $id = self::getInstanceId($instance->modx);
        self::$instances[$id] = $instance;
    }

    public static function hasInstance(\modX $modx): bool
    {
        $id = self::getInstanceId($modx);
        return isset(self::$instances[$id]);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function modx(): \modX
    {
        return $this->modx;
    }

    public function manager(): Manager
    {
        return $this->manager;
    }

    public function log(string $message): void
    {
        if (\method_exists($this->modx, 'log')) {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, $message);
        }
    }

    protected function processBootstrapAutoload(): void
    {
        [$modx, ] = $this->getReferences();

        $componentPath = MODX_CORE_PATH . 'components/';
        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $modx->call(\modNamespace::class, 'loadCache', [$modx]);
        $namespacesBootstrap = $this->manager->getNamespacesBootstrap($componentPath, $namespaces);

        $packagesBootstrap = $this->manager->getPackagesBootstrap($componentPath);
        $loadableBootstrap = \array_intersect_key($namespacesBootstrap, $packagesBootstrap);

        foreach ($loadableBootstrap as $bootstrap) {
            $loadableBootstrap[$bootstrap] = $this->loadBootstrap($bootstrap);
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

    /**
     * @psalm-type namespace = string
     * @psalm-type connector = string
     * @psalm-type extra = string
     *
     * @return array{
     *  namespace,
     *  connector,
     *  extra,
     * }|null
     */
    protected function getConnectorRequest(): ?array
    {
        if (empty($_SERVER['REQUEST_URI'])) {
            return null;
        }

        $matches = [];
        $pattern = '#^/assets/components/([^/]+)/api/([^/.]+)/(.*)?#';

        if (\preg_match($pattern, $_SERVER['REQUEST_URI'], $matches)) {
            return [
                $matches[1],
                $matches[2],
                $matches[3] ?? '',
            ];
        }

        return null;
    }

    protected function processConnectorRequest(): void
    {
        $request = $this->getConnectorRequest();

        if ($request === null) {
            return;
        }

        [$namespace, $connector, ] = $request;
        [$modx, $container] = $this->getReferences();

        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $modx->call(\modNamespace::class, 'loadCache', [$modx]);

        if (!isset($namespaces[$namespace])) {
            return;
        }

        $connectorPath = \sprintf(
            '%s/assets/components/%s/api/%s.php',
            $_SERVER['DOCUMENT_ROOT'] ?? '',
            $namespace,
            $connector,
        );

        if (!\file_exists($connectorPath)) {
            return;
        }

        try {
            require $connectorPath;
            exit;
        } catch (\Throwable $e) {
            $showErrors = (bool) $this->config->getSetting('show_errors')?->getValue();
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
    }

    protected function loadBootstrap(string $file): bool
    {
        if (!\file_exists($file)) {
            return false;
        }

        [$modx, $container] = $this->getReferences();

        try {
            require $file;
            return true;
        } catch (\Throwable $e) {
            $showErrors = (bool) $this->config->getSetting('show_errors')?->getValue();
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

    /**
     * @return array{
     * \modX,
     * Container,
     * }
     */
    protected function getReferences(): array
    {
        /** @psalm-suppress UnsupportedPropertyReferenceUsage */
        $modx = &$this->modx;

        /** @psalm-suppress UnsupportedPropertyReferenceUsage */
        $container = &$this->container;

        return [$modx, $container];
    }
}
