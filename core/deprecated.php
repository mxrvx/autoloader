<?php

declare(strict_types=1);

if (!\class_exists('\\xPDO') && \class_exists('\\xPDO\\xPDO')) {
    \class_alias('\\xPDO\\xPDO', '\\xPDO');
}
if (!\class_exists('\\modNamespace') && \class_exists('\\MODX\\Revolution\\modNamespace')) {
    \class_alias('\\MODX\\Revolution\\modNamespace', '\\modNamespace');
}
if (!\class_exists('\\modExtensionPackage') && \class_exists('\\MODX\\Revolution\\modExtensionPackage')) {
    \class_alias('\\MODX\\Revolution\\modExtensionPackage', '\\modExtensionPackage');
}
if (!\class_exists('\\modSystemSetting') && \class_exists('\\MODX\\Revolution\\modSystemSetting')) {
    \class_alias('\\MODX\\Revolution\\modSystemSetting', '\\modSystemSetting');
}
