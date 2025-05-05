<?php


declare(strict_types=1);

namespace MXRVX\Autoloader\Composer\Package;

/**
 * @psalm-import-type ComposerPackageDependencyStructure from Dependency
 *
 * @implements \IteratorAggregate<string, Dependency>
 */
class Dependencies implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /** @var array<string, Dependency> */
    private array $dependencies = [];

    private function __construct(array $dependencies = [])
    {
        /** @var Dependency[] $dependencies */
        foreach ($dependencies as $dependency) {
            $this->add($dependency);
        }
    }

    public static function fromArray(array $dependencies = []): self
    {
        if (self::isAssoc($dependencies)) {
            $dependencies = \array_map(
                static fn($name, $version) => ['name' => $name, 'version' => $version],
                \array_keys($dependencies),
                $dependencies,
            );
        }

        /** @var ComposerPackageDependencyStructure[] $dependencies */
        $dependencies = \array_filter(
            $dependencies,
            static fn(array $dependency): bool => Dependency::validate($dependency),
        );

        $dependencies = \array_map(
            static fn(array $dependency) => new Dependency(
                name: $dependency['name'] ?? '',
                version: $dependency['version'] ?? '',
            ),
            $dependencies,
        );

        return new self($dependencies);
    }

    public static function fromCollection(array $dependencies = []): self
    {
        $dependencies = \array_filter(
            $dependencies,
            static fn(object $dependency): bool => $dependency instanceof Dependency,
        );

        return new self($dependencies);
    }

    public function count(): int
    {
        return \count($this->dependencies);
    }

    public function has(string $name): bool
    {
        return isset($this->dependencies[$name]);
    }

    public function remove(string $name): void
    {
        unset($this->dependencies[$name]);
    }

    public function add(Dependency $dependency): void
    {
        $this->dependencies[$dependency->name] = $dependency;
    }

    public function get(string $name): ?Dependency
    {
        return $this->dependencies[$name] ?? null;
    }

    /**
     * @return array<string, Dependency>
     */
    public function all(): array
    {
        return $this->dependencies;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->dependencies);
    }

    public function getNames(): array
    {
        return \array_keys($this->dependencies);
    }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->dependencies as $name => $dependency) {
            $result[$name] = $dependency;
        }
        return $result;
    }

    private static function isAssoc(array $arr): bool
    {
        if ($arr === []) {
            return false;
        }

        return !Dependency::validate((array) \reset($arr));
    }
}
