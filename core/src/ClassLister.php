<?php


declare(strict_types=1);

namespace MXRVX\Autoloader;

class ClassLister
{
    /** @var array<int, class-string> */
    protected static array $list = [];

    /**
     * @return array<int, class-string>
     */
    public static function classes(): array
    {
        if (empty(self::$list)) {
            $defined = \array_merge(
                \get_declared_traits(),
                \get_declared_interfaces(),
                \get_declared_classes(),
            );

            $classmapFile = MODX_CORE_PATH . '/vendor/composer/autoload_classmap.php';

            if (\file_exists($classmapFile)) {
                $classmap = require $classmapFile;
                \assert(\is_array($classmap));
                if ($classmap) {
                    $defined = \array_merge($defined, \array_keys($classmap));
                }
            }

            /** @var array<int, class-string> */
            self::$list = \array_values(\array_unique(\array_filter($defined, static function ($class) {
                return \is_string($class);
            })));
        }

        return self::$list;
    }

    /**
     * @param non-empty-string $pattern
     * @return array<int, class-string>
     */
    public static function findByRegex(string $pattern): array
    {
        if (empty($pattern)) {
            return [];
        }

        /** @var array<array-key, array<int, class-string>> $cache */
        static $cache = [];

        if (isset($cache[$pattern])) {
            return $cache[$pattern];
        }

        /** @var array<int, class-string> $classes */
        $classes = [];

        foreach (self::classes() as $class) {
            if (\preg_match($pattern, $class)) {
                $classes[] = $class;
            }
        }

        $cache[$pattern] = $classes;

        return $classes;
    }
}
