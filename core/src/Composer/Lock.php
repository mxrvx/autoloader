<?php


declare(strict_types=1);

namespace MXRVX\Autoloader\Composer;

use MXRVX\Autoloader\Composer\Package\Package;
use MXRVX\Autoloader\Composer\Package\Packages;

/**
 * @psalm-import-type ComposerPackageStructure from Package
 *
 * @psalm-type ComposerLockStructure = array{
 *     packages: array<ComposerPackageStructure>,
 * }
 */
class Lock
{
    private bool $isValid;

    private function __construct(private readonly Packages $packages)
    {
        $this->isValid = $this->packages->count() > 0;
    }

    public static function fromArray(array $packages): self
    {
        return new self(Packages::fromArray($packages));
    }

    public static function fromCacheFile(string $path): self
    {
        return self::fromFile($path);
    }

    public static function fromLockFile(string $path): self
    {
        $instance = self::fromFile($path);
        $instance->packages->index();
        $instance->packages->sort();
        return $instance;
    }

    /**
     * @psalm-assert-if-true ComposerLockStructure $row
     */
    public static function validatePackages(array $row): bool
    {
        return isset($row['packages']) && \is_array($row['packages']);
    }

    public function getPackages(): Packages
    {
        return $this->packages;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    private static function fromFile(string $path): self
    {
        $data = [];
        if (\is_string($content = @\file_get_contents($path))) {
            /** @var array $data */
            $data = \json_decode($content, true);
            if (\json_last_error() !== JSON_ERROR_NONE) {
                $data = [];
            }
        }

        $packages = [];
        if (self::validatePackages($data)) {
            $packages = $data['packages'];
        }

        return self::fromArray($packages);
    }
}
