<?php


declare(strict_types=1);

namespace MXRVX\Autoloader\Composer\Package;

/**
 * @psalm-type ComposerPackageDependencyStructure = array{
 *     name: string,
 *     version: string,
 * }
 *
 */
class Dependency implements \JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
    ) {}

    public static function validate(array $row): bool
    {
        return isset($row['name']) && \is_string($row['name']) &&
            isset($row['version']) && \is_string($row['version']);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
        ];
    }
}
