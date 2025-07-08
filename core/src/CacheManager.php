<?php

declare(strict_types=1);

namespace MXRVX\Autoloader;

class CacheManager
{
    public function __construct(protected \modX $modx) {}

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

    public function clearCache(bool $fireOnCacheUpdateEvent = true): void
    {
        self::deleteDirectory(MODX_CORE_PATH . 'cache/', true);

        if ($fireOnCacheUpdateEvent) {
            $this->modx->invokeEvent('OnCacheUpdate', [
                'results' => ['all' => true],
                'paths' => [],
                'options' => ['all'],
            ]);
        }
    }
}
