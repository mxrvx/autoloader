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

        if (self::hasInstance($modx)) {
            $instance = self::getInstance($modx);
            $this->config = $instance->config;
            $this->manager = $instance->manager;
            $this->container = $instance->container;
        } else {
            $this->config = Config::make($modx->config);
            $this->manager = Manager::create(MODX_BASE_PATH . 'composer.lock', \dirname(__DIR__) . '/packages');
            $this->container = $this->createContainer();

            self::setInstance($this);

            $this->processAutoloader();
            $this->processConnectorRequest();
        }

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

    public static function getModxInstance(): \modX
    {
        $modx = null;

        foreach (self::$instances as $instance) {
            if ($instance instanceof self) {
                $modx = $instance->modx();
                break;
            }
        }

        return $modx instanceof \modX ? $modx : \modX::getInstance();
    }

    public static function clearCache(bool $fireOnCacheUpdateEvent = true): void
    {
        self::deleteDirectory(MODX_CORE_PATH . 'cache/', true);

        if ($fireOnCacheUpdateEvent && $modx = self::getModxInstance()) {
            $modx->invokeEvent('OnCacheUpdate', [
                'results' => ['all' => true],
                'paths' => [],
                'options' => ['all'],
            ]);
        }
    }

    /**
     * @see http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
     *
     */
    public static function deleteDirectory(string $directory, bool $contentOnly = false): bool
    {
        if (!\is_dir($directory)) {
            return false;
        }

        /** @var \SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                \rmdir($file->getRealPath());
            } else {
                self::deleteFile($file->getRealPath());
            }
        }

        if (!$contentOnly) {
            return \rmdir($directory);
        }

        return true;
    }

    public static function deleteFile(string $filename): bool
    {
        if (\file_exists($filename)) {
            $result = \unlink($filename);

            //Wiping out changes in local file cache
            \clearstatcache(false, $filename);

            return $result;
        }

        return false;
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

    /**
     * @return array<string, string>
     */
    public function getAutoloaders(): array
    {
        [$modx, ] = $this->getReferences();

        $componentPath = MODX_CORE_PATH . 'components/';
        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $modx->call(\modNamespace::class, 'loadCache', [$modx]);
        $namespacesAutoloader = $this->manager->getNamespacesAutoloader($componentPath, $namespaces);

        $packagesAutoloader = $this->manager->getPackagesAutoloader($componentPath);

        return \array_intersect_key($namespacesAutoloader, $packagesAutoloader);
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
    public function getConnectorRequest(): ?array
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

    protected function processAutoloader(): void
    {
        [$modx, $container] = $this->getReferences();

        $showErrors = $this->config->getSetting('show_errors')?->getBoolValue();

        $autoloaders = $this->getAutoloaders();
        foreach ($autoloaders as $autoloader) {
            if (\file_exists($autoloader)) {

                try {
                    require $autoloader;

                    $autoloaders[$autoloader] = true;

                } catch (\Throwable $e) {
                    $autoloaders[$autoloader] = false;

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
            } else {
                $autoloaders[$autoloader] = null;
            }
        }


        if ($this->config->getSetting('show_loads')?->getBoolValue()) {
            $this->log(
                \sprintf(
                    'loaded `%s` of autoloaders `%s`',
                    \count($autoloaders),
                    \var_export($autoloaders, true),
                ),
            );
        }
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
            if ($this->config->getSetting('show_errors')?->getBoolValue()) {
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

    /**
     * @throws \Exception
     */
    protected function createContainer(): Container
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();
        $container->set(\modX::class, $this->modx);

        return $container;
    }
}
