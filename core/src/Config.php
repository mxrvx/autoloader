<?php


declare(strict_types=1);

namespace MXRVX\Autoloader;

use MXRVX\Schema\System\Settings;
use MXRVX\Schema\System\Settings\SchemaConfig;

class Config extends SchemaConfig
{
    public static function make(array $config): SchemaConfig
    {
        $schema = Settings\Schema::define(App::AUTOLOADER)
            ->withSettings(
                [
                    Settings\Setting::define(
                        key: 'show_errors',
                        value: false,
                        xtype: 'combo-boolean',
                        typecast: Settings\TypeCaster::BOOLEAN,
                    ),
                    Settings\Setting::define(
                        key: 'show_loads',
                        value: false,
                        xtype: 'combo-boolean',
                        typecast: Settings\TypeCaster::BOOLEAN,
                    ),
                ],
            );
        return SchemaConfig::define($schema)->withConfig($config);
    }
}
