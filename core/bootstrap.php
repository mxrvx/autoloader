<?php

declare(strict_types=1);

if (!\defined('MODX_CORE_PATH')) {
    exit('Could not load MODX core');
}

require MODX_CORE_PATH . 'vendor/autoload.php';

/** @var \modX $modx */
if (!isset($modx)) {
    if (\file_exists(MODX_CORE_PATH . 'model/modx/modx.class.php')) {
        require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    }
    $modx = \modX::getInstance();
    $modx->initialize();
}
