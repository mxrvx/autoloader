<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Composer\Package;

/**
 * @psalm-import-type ComposerPackageStructure from Package
 * @psalm-import-type ComposerPackageDependencyStructure from Dependency
 *
 * @implements \IteratorAggregate<string, Package>
 */
class Packages implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @var array<string, Package> */
    private array $packages = [];

    private function __construct(array $packages = [])
    {
        /** @var Package[] $packages */
        foreach ($packages as $package) {
            $this->add($package);
        }
    }

    public static function fromArray(array $packages = []): self
    {

        /** @var ComposerPackageStructure[] $packages */
        $packages = \array_filter(
            $packages,
            static fn(array $row): bool => Package::validate($row),
        );

        /** @var Package[] $packages */
        $packages = \array_map(
            static fn(array $row) => new Package(
                name: $row['name'],
                version : $row['version'],
                require: $row['require'] ?? [],
                bin: $row['bin'] ?? null,
                dependencies: isset($row['dependencies']) && \is_array($row['dependencies']) ? Dependencies::fromArray($row['dependencies']) : null,
            ),
            $packages,
        );

        return new self($packages);
    }

    public static function fromCollection(array $packages = []): self
    {
        $packages = \array_filter(
            $packages,
            static fn(object $package): bool => $package instanceof Package,
        );

        return new self($packages);
    }

    public function count(): int
    {
        return \count($this->packages);
    }

    public function has(string $name): bool
    {
        return isset($this->packages[$name]);
    }

    public function remove(string $name): void
    {
        unset($this->packages[$name]);
    }

    public function add(Package $package): void
    {
        $this->packages[$package->name] = $package;
    }

    public function get(string $name): ?Package
    {
        return $this->packages[$name] ?? null;
    }

    public function getByNameSpace(string $namespace): ?Package
    {
        foreach ($this->packages as $package) {
            if ($package->namespace === $namespace) {
                return $package;
            }
        }
        return null;
    }

    /**
     * @return array<string, Package>
     */
    public function all(): array
    {
        return $this->packages;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->packages);
    }

    public function getNames(): array
    {
        return \array_keys($this->packages);
    }

    public function index(): void
    {
        /** @var array<string, ComposerPackageDependencyStructure> $packageDependencies */
        $packageDependencies = [];
        $packageNames = \array_flip($this->getNames());
        foreach ($this->packages as $package) {
            $packageDependencies[$package->name] = \array_intersect_key($package->require, $packageNames);
        }

        foreach ($this->packages as $package) {
            /** @var array<string, ComposerPackageDependencyStructure> $packageDependencies */
            $dependencies = [];
            $package->setDependencies(Dependencies::fromArray($this->collectAllDependencies($package->name, $packageDependencies, $dependencies)));
        }
    }

    public function sort(): void
    {
        $sorted = \array_values($this->packages);

        \usort($sorted, static function (Package $a, Package $b) {
            return $a->getDependencies()?->count() <=> $b->getDependencies()?->count();
        });

        $this->packages = self::fromCollection($sorted)->packages;
    }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->packages as $name => $package) {
            $result[$name] = $package;
        }
        return $result;
    }

    /**
     * @param array<string, array<string, string>> $allPackages
     * @param array<string, bool> $visited
     * @return array<string, string>
     */
    private function collectAllDependencies(string $packageName, array $allPackages, array &$visited = []): array
    {
        if (isset($visited[$packageName])) {
            return [];
        }
        $visited[$packageName] = true;

        $result = [];

        if (!isset($allPackages[$packageName])) {
            return [];
        }

        foreach ($allPackages[$packageName] as $depName => $version) {
            $result[$depName] = $version;
            $subDeps = $this->collectAllDependencies($depName, $allPackages, $visited);
            foreach ($subDeps as $subDepName => $subVersion) {
                if (!isset($result[$subDepName])) {
                    $result[$subDepName] = $subVersion;
                }
            }
        }

        return $result;
    }
}
