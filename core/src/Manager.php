<?php


declare(strict_types=1);

namespace MXRVX\Autoloader;

use MXRVX\Autoloader\Composer\Lock;
use MXRVX\Autoloader\Composer\Package\Package;
use MXRVX\Autoloader\Composer\Package\Packages;

/**
 * @psalm-type ArrayNamespaceStructure = array{
 *     name: string,
 *     path: string,
 * }
 *
 */
class Manager
{
    private Lock $lock;

    private function __construct(
        private readonly string $lockFilePath,
        private readonly string $cacheDir,
    ) {
        if (!$cacheFilePath = $this->getCacheFilePath()) {
            $this->lock = Lock::fromArray([]);
        } else {
            if ($this->isCacheValid($cacheFilePath)) {
                $this->lock = Lock::fromCacheFile($cacheFilePath);
            } else {
                $this->lock = Lock::fromLockFile($lockFilePath);
                $this->saveCache($cacheFilePath);
            }
        }
    }

    public static function create(string $lockFilePath, string $cacheDir): self
    {
        return new self($lockFilePath, \rtrim($cacheDir, DIRECTORY_SEPARATOR));
    }

    public function getLockFile(): string
    {
        return $this->lockFilePath;
    }

    public function hasValidLock(): bool
    {
        return $this->lock->isValid();
    }

    public function getPackages(): Packages
    {
        return $this->lock->getPackages();
    }

    /**
     * @param string $name Package name or namespace
     */
    public function getPackage(string $name): ?Package
    {
        if (\str_contains($name, '/')) {
            return $this->getPackages()->get($name);
        }
        return $this->getPackages()->getByNameSpace($name);

    }

    /**
     * @param string $name Package name or namespace
     * @param bool $onlyBine filter only bin packages
     * @return array<string, Package>
     */
    public function getPackageDependencies(string $name, bool $onlyBine = false): array
    {
        $result = [];

        $dependencies = $this->getPackage($name)?->getDependencies()->all();
        if ($dependencies) {
            foreach ($dependencies as $dependency) {
                if (!$package = $this->getPackage($dependency->name)) {
                    continue;
                }
                if (!$onlyBine) {
                    $result[$dependency->name] = $package;
                } elseif ($package->isBin()) {
                    $result[$dependency->name] = $package;
                }
            }
        }

        return $result;
    }

    /**
     * @param array<ArrayNamespaceStructure> $namespaces
     * @return array<string, string>
     */
    public function getNamespacesBootstrap(string $componentPath, array $namespaces): array
    {
        $result = [];
        foreach ($namespaces as $namespace) {
            $packageSpace = (string) ($namespace['path'] ?? '');
            if (empty($packageSpace)) {
                continue;
            }
            $bootstrap = \rtrim($packageSpace, '/') . '/bootstrap.php';
            if (\str_starts_with($bootstrap, $componentPath)) {
                $result[$bootstrap] = $bootstrap;
            }
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    public function getPackagesBootstrap(string $componentPath): array
    {
        $result = [];
        foreach ($this->getPackages()->all() as $package) {
            if ($bootstrap = $package->getBootstrap($componentPath)) {
                $result[$bootstrap] = $bootstrap;
            }
        }
        return $result;
    }

    private function isCacheValid(string $path): bool
    {
        return \file_exists($path);
    }

    private function getCacheFilePath(): ?string
    {
        if (!\file_exists($this->lockFilePath)) {
            return null;
        }
        return \sprintf('%s/packages_%s.json', $this->cacheDir, \filemtime($this->lockFilePath));
    }

    private function saveCache(string $cacheFile): void
    {
        $this->prepareCacheDir();
        $packages = $this->getPackages();
        if ($packages->count()) {
            $content = \json_encode(['packages' => $packages], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if (\file_put_contents($cacheFile, $content) === false) {
                throw new \RuntimeException(\sprintf('Failed to save cache file: `%s`', $cacheFile));
            }
        }
    }

    private function prepareCacheDir(): void
    {
        if (!\file_exists($this->cacheDir)) {
            if (!\mkdir($this->cacheDir, 0755, true) && !\is_dir($this->cacheDir)) {
                throw new \RuntimeException("Failed to create cache directory");
            }
        }

        \array_map('unlink', \glob(\sprintf('%s/packages_*.json', $this->cacheDir)));
    }
}
