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

    public function getBootstrap(string $componentPath): ?string
    {
        return $this->isBin() && !$this->isAutoloader() ? \rtrim($componentPath, '/') . '/' . $this->namespace . '/bootstrap.php' : null;
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
