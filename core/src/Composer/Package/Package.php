<?php


declare(strict_types=1);

namespace MXRVX\Autoloader\Composer\Package;

use MXRVX\Autoloader\App;

/**
 * @psalm-type ComposerPackageStructure = array{
 *     name: string,
 *     version: string,
 *     require: array|array<array-key, string>,
 *     bin: null|list<string>,
 *     dependencies:null|array,
 * }
 *
 * @psalm-type DataPackageStructure = array<string, array<int, ComposerPackageStructure>>
 *
 * @psalm-type DataPackagesStructure = array{
 *     packages: array<string, DataPackageStructure>
 * }
 */
class Package implements \JsonSerializable
{
    public readonly string $namespace;

    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly array  $require,
        public readonly ?array $bin = [],
        private ?Dependencies  $dependencies = null,
    ) {
        $this->namespace = \str_replace('/', '-', \strtolower($this->name));
    }

    public static function validate(array $row): bool
    {
        return isset($row['name']) && \is_string($row['name']) &&
            isset($row['version']) && \is_string($row['version']) &&
            isset($row['require']) && \is_array($row['require']);
    }

    public function setDependencies(Dependencies $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    public function getDependencies(): ?Dependencies
    {
        return $this->dependencies;
    }

    public function isBin(): bool
    {
        return !empty($this->bin);
    }

    public function isAutoloader(): bool
    {
        return $this->namespace === App::NAMESPACE;
    }

    public function getAutoloaderFile(string $componentPath): ?string
    {
        return $this->isBin() && !$this->isAutoloader() ? \rtrim($componentPath, '/') . '/' . $this->namespace . '/autoloader.php' : null;
    }

    public function getPackagistUrl(): string
    {
        return \sprintf('https://repo.packagist.org/p2/%s.json', $this->name);
    }

    /**
     * @return string[]|null
     */
    public function getPackagistVersions(): ?array
    {
        static $content;

        if ($content === null) {
            $content = @\file_get_contents(
                $this->getPackagistUrl(),
                false,
                \stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                    ],
                ]),
            );
        }

        $result = [];
        if (\is_string($content)) {
            /** @var DataPackagesStructure $data */
            $data = \json_decode($content, true);
            if (\json_last_error() !== JSON_ERROR_NONE) {
                $data = [];
            }
            /** @var DataPackageStructure $packages */
            $packages = $data['packages'] ?? [];

            /** @var ComposerPackageStructure $package */
            $package = $packages[$this->name] ?? [];
            if (\is_array($package)) {
                $result = \array_column($package, 'version');
                $result = \array_map(
                    static fn($version) => (string) $version,
                    $result,
                );
            }
        }

        return \count($result) ? $result : null;
    }

    /**
     * @return string[]
     */
    public function getAvailableVersions(): array
    {
        $result = [];
        if ($versions = $this->getPackagistVersions()) {
            foreach ($versions as $version) {
                if (\version_compare($this->version, $version, '<')) {
                    $result[] = $version;
                }
            }
        }
        return $result;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'namespace' => $this->namespace,
            'version' => $this->version,
            'require' => $this->require,
            'bin' => $this->bin,
            'dependencies' => $this->dependencies,
        ];
    }
}
