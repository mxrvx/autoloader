<?php

declare(strict_types=1);

namespace MXRVX\Autoloader;

use DI\Container;
use DI\ContainerBuilder;
use MXRVX\Schema\System\Settings\SchemaConfigInterface;

class App
{
    public const NAMESPACE = 'mxrvx-autoloader';
    public const AUTOLOADER = '_autoloader';

    public SchemaConfigInterface $config;
    protected static ?self $instance = null;
    protected PackageManager $packageManager;
    protected CacheManager $cacheManager;
    protected Container $container;

    /**
     * @internal
     * @throws \Exception
     */
    public function __construct(protected \modX $modx)
    {
        \Composer\InstalledVersions::getAllRawData();

        $this->config = Config::make($modx->config);
        $this->packageManager = new PackageManager(MODX_BASE_PATH . 'composer.lock', \dirname(__DIR__) . '/packages');
        $this->cacheManager = new CacheManager($modx);
        $this->container = $this->createContainer();

        self::$instance = $this;

        $this->runHandlers();

        if (!$this->packageManager->hasValidLock()) {
            $this->log(
                \sprintf(
                    '`%s` is not valid composer.lock file',
                    $this->packageManager->getLockFile(),
                ),
            );
        }
    }

    public static function hasInstance(): bool
    {
        return isset(self::$instance);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('AutoloaderApp instance is not initialized');
        }
        return self::$instance;
    }

    public static function container(): Container
    {
        return self::getInstance()->getContainer();
    }

    public static function packageManager(): PackageManager
    {
        return self::getInstance()->getPackageManager();
    }

    public static function cacheManager(): CacheManager
    {
        return self::getInstance()->getCacheManager();
    }

    public static function modx(): \modX
    {
        return self::getInstance()->getModx();
    }

    /**
     * @return class-string[]
     */
    public function getHandlerClasses(): array
    {
        return [
            Handlers\AutoloaderHandler::class,
            Handlers\ConnectorHandler::class,
        ];
    }

    public function runHandlers(): void
    {
        foreach ($this->getHandlerClasses() as $className) {
            /** @var callable $handler */
            $handler = $this->container->get($className);
            if (\is_callable($handler)) {
                $handler();
            }
        }
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getModx(): \modX
    {
        return $this->modx;
    }

    public function getPackageManager(): PackageManager
    {
        return $this->packageManager;
    }

    public function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }

    public function log(string $message, int $level = \modX::LOG_LEVEL_ERROR): void
    {
        if (\method_exists($this->modx, 'log')) {
            $this->modx->log($level, $message);
        }
    }

    /**
     * @return array{
     * \modX,
     * Container,
     * }
     */
    public function getReferences(): array
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

        $handlers = $this->getHandlerClasses();
        $builder->addDefinitions(\array_merge([
            App::class => $this,
            \modX::class => $this->modx,
            PackageManager::class => $this->packageManager,
            CacheManager::class => $this->cacheManager,
        ], \array_combine(
            $handlers,
            \array_map(static fn($class) => \DI\autowire(), $handlers),
        )));

        return $builder->build();
    }
}
