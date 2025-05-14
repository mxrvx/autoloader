<?php

declare(strict_types=1);

if (!\defined('MODX_CORE_PATH')) {

    $dir = __DIR__;
    while (!\str_ends_with($dir, DIRECTORY_SEPARATOR)) {
        $dir = \dirname($dir);

        $file = \implode(DIRECTORY_SEPARATOR, [$dir, 'core', 'config', 'config.inc.php']);
        if (\file_exists($file)) {
            require_once $file;
            break;
        }
    }
    unset($dir);

    if (!\defined('MODX_CORE_PATH')) {
        exit('Could not load MODX core');
    }
}

$file = MODX_CORE_PATH . 'vendor/autoload.php';
if (\file_exists($file)) {
    require_once $file;
}
unset($file);

/** @psalm-suppress MissingFile */
require_once __DIR__ . '/deprecated.php';

/** @var \modX $modx */
if (!isset($modx)) {
    if (\file_exists(MODX_CORE_PATH . 'model/modx/modx.class.php')) {
        require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    }
    $modx = \modX::getInstance();
    $modx->initialize();
}
