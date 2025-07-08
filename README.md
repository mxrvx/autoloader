# Automatic loading `autoloader.php` packages installed using composer for MODX Revolution

## Installation and configuration

Install the package via composer:

## Install
```
composer require --dev mxrvx/autoloader
composer exec mxrvx-autoloader install
```

## Remove
```
composer exec mxrvx-autoloader remove
composer remove mxrvx/autoloader
```


[![PHP](https://img.shields.io/packagist/php-v/mxrvx/autoloader.svg?style=flat-square&logo=php)](https://packagist.org/packages/mxrvx/autoloader)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mxrvx/autoloader.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/mxrvx/autoloader)
[![License](https://img.shields.io/packagist/l/mxrvx/autoloader.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/mxrvx/autoloader.svg?style=flat-square)](https://packagist.org/packages/mxrvx/autoloader)

## Settings

* `show_errors` - show error when loading packages
* `show_loads` - show information when loading packages

## Usage

### Get packages

```php
$packages = \MXRVX\Autoloader\App::packageManager()->getPackages();
var_export($packages);

\MXRVX\Autoloader\Composer\Package\Packages::__set_state(array(
   'packages' => 
  array (
    'psr/container' => 
    \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
       'namespace' => 'psr-container',
       'name' => 'psr/container',
       'version' => '2.0.2',
       'require' => 
      array (
        'php' => '>=7.4.0',
      ),
       'bin' => NULL,
       'dependencies' => 
      \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
         'dependencies' => 
        array (
        ),
      )),
    )),
    'symfony/console' => 
    \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
       'namespace' => 'symfony-console',
       'name' => 'symfony/console',
       'version' => 'v5.4.47',
       'require' => 
      array (
        'php' => '>=7.2.5',
        'symfony/deprecation-contracts' => '^2.1|^3',
        'symfony/polyfill-mbstring' => '~1.0',
        'symfony/polyfill-php73' => '^1.9',
        'symfony/polyfill-php80' => '^1.16',
        'symfony/service-contracts' => '^1.1|^2|^3',
        'symfony/string' => '^5.1|^6.0',
      ),
       'bin' => NULL,
       'dependencies' => 
      \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
         'dependencies' => 
        array (
          'symfony/deprecation-contracts' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/deprecation-contracts',
             'version' => '^2.1|^3',
          )),
          'symfony/polyfill-mbstring' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-mbstring',
             'version' => '~1.0',
          )),
          'symfony/polyfill-php73' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-php73',
             'version' => '^1.9',
          )),
          'symfony/polyfill-php80' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-php80',
             'version' => '^1.16',
          )),
          'symfony/service-contracts' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/service-contracts',
             'version' => '^1.1|^2|^3',
          )),
          'psr/container' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'psr/container',
             'version' => '^1.1|^2.0',
          )),
          'symfony/string' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/string',
             'version' => '^5.1|^6.0',
          )),
          'symfony/polyfill-ctype' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-ctype',
             'version' => '~1.8',
          )),
          'symfony/polyfill-intl-grapheme' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-intl-grapheme',
             'version' => '~1.0',
          )),
          'symfony/polyfill-intl-normalizer' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-intl-normalizer',
             'version' => '~1.0',
          )),
        ),
      )),
    )),
    'mxrvx/autoloader' => 
    \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
       'namespace' => 'mxrvx-autoloader',
       'name' => 'mxrvx/autoloader',
       'version' => 'dev-main',
       'require' => 
      array (
        'ext-json' => '*',
        'ext-pdo' => '*',
        'mxrvx/schema-system-settings' => '^1.0.0',
        'php' => '>=8.1',
        'symfony/console' => '^5.4',
      ),
       'bin' => 
      array (
        0 => 'bin/mxrvx-autoloader',
      ),
       'dependencies' => 
      \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
         'dependencies' => 
        array (
          'mxrvx/schema-system-settings' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'mxrvx/schema-system-settings',
             'version' => '^1.0.0',
          )),
          'symfony/console' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/console',
             'version' => '^5.4',
          )),
          'symfony/deprecation-contracts' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/deprecation-contracts',
             'version' => '^2.1|^3',
          )),
          'symfony/polyfill-mbstring' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-mbstring',
             'version' => '~1.0',
          )),
          'symfony/polyfill-php73' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-php73',
             'version' => '^1.9',
          )),
          'symfony/polyfill-php80' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-php80',
             'version' => '^1.16',
          )),
          'symfony/service-contracts' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/service-contracts',
             'version' => '^1.1|^2|^3',
          )),
          'psr/container' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'psr/container',
             'version' => '^1.1|^2.0',
          )),
          'symfony/string' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/string',
             'version' => '^5.1|^6.0',
          )),
          'symfony/polyfill-ctype' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-ctype',
             'version' => '~1.8',
          )),
          'symfony/polyfill-intl-grapheme' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-intl-grapheme',
             'version' => '~1.0',
          )),
          'symfony/polyfill-intl-normalizer' => 
          \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
             'name' => 'symfony/polyfill-intl-normalizer',
             'version' => '~1.0',
          )),
        ),
      )),
    )),
  ),
))
}
```

### Get package dependencies

```php
$packages = \MXRVX\Autoloader\App::packageManager()->getPackageDependencies('mxrvx/autoloader', $onlyBine = false);
var_export($dependencies);

    array (
      'mxrvx/schema-system-settings' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'mxrvx-schema-system-settings',
         'name' => 'mxrvx/schema-system-settings',
         'version' => 'v1.0.2',
         'require' => 
        array (
          'ext-json' => '*',
          'ext-pdo' => '*',
          'php' => '>=8.0',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/console' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-console',
         'name' => 'symfony/console',
         'version' => 'v5.4.47',
         'require' => 
        array (
          'php' => '>=7.2.5',
          'symfony/deprecation-contracts' => '^2.1|^3',
          'symfony/polyfill-mbstring' => '~1.0',
          'symfony/polyfill-php73' => '^1.9',
          'symfony/polyfill-php80' => '^1.16',
          'symfony/service-contracts' => '^1.1|^2|^3',
          'symfony/string' => '^5.1|^6.0',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
            'symfony/deprecation-contracts' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/deprecation-contracts',
               'version' => '^2.1|^3',
            )),
            'symfony/polyfill-mbstring' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-mbstring',
               'version' => '~1.0',
            )),
            'symfony/polyfill-php73' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-php73',
               'version' => '^1.9',
            )),
            'symfony/polyfill-php80' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-php80',
               'version' => '^1.16',
            )),
            'symfony/service-contracts' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/service-contracts',
               'version' => '^1.1|^2|^3',
            )),
            'psr/container' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'psr/container',
               'version' => '^1.1|^2.0',
            )),
            'symfony/string' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/string',
               'version' => '^5.1|^6.0',
            )),
            'symfony/polyfill-ctype' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-ctype',
               'version' => '~1.8',
            )),
            'symfony/polyfill-intl-grapheme' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-intl-grapheme',
               'version' => '~1.0',
            )),
            'symfony/polyfill-intl-normalizer' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-intl-normalizer',
               'version' => '~1.0',
            )),
          ),
        )),
      )),
      'symfony/deprecation-contracts' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-deprecation-contracts',
         'name' => 'symfony/deprecation-contracts',
         'version' => 'v3.5.1',
         'require' => 
        array (
          'php' => '>=8.1',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/polyfill-mbstring' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-polyfill-mbstring',
         'name' => 'symfony/polyfill-mbstring',
         'version' => 'v1.32.0',
         'require' => 
        array (
          'ext-iconv' => '*',
          'php' => '>=7.2',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/polyfill-php73' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-polyfill-php73',
         'name' => 'symfony/polyfill-php73',
         'version' => 'v1.32.0',
         'require' => 
        array (
          'php' => '>=7.2',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/polyfill-php80' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-polyfill-php80',
         'name' => 'symfony/polyfill-php80',
         'version' => 'v1.32.0',
         'require' => 
        array (
          'php' => '>=7.2',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/service-contracts' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-service-contracts',
         'name' => 'symfony/service-contracts',
         'version' => 'v3.5.1',
         'require' => 
        array (
          'php' => '>=8.1',
          'psr/container' => '^1.1|^2.0',
          'symfony/deprecation-contracts' => '^2.5|^3',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
            'psr/container' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'psr/container',
               'version' => '^1.1|^2.0',
            )),
            'symfony/deprecation-contracts' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/deprecation-contracts',
               'version' => '^2.5|^3',
            )),
          ),
        )),
      )),
      'psr/container' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'psr-container',
         'name' => 'psr/container',
         'version' => '2.0.2',
         'require' => 
        array (
          'php' => '>=7.4.0',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/string' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-string',
         'name' => 'symfony/string',
         'version' => 'v6.4.21',
         'require' => 
        array (
          'php' => '>=8.1',
          'symfony/polyfill-ctype' => '~1.8',
          'symfony/polyfill-intl-grapheme' => '~1.0',
          'symfony/polyfill-intl-normalizer' => '~1.0',
          'symfony/polyfill-mbstring' => '~1.0',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
            'symfony/polyfill-ctype' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-ctype',
               'version' => '~1.8',
            )),
            'symfony/polyfill-intl-grapheme' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-intl-grapheme',
               'version' => '~1.0',
            )),
            'symfony/polyfill-intl-normalizer' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-intl-normalizer',
               'version' => '~1.0',
            )),
            'symfony/polyfill-mbstring' => 
            \MXRVX\Autoloader\Composer\Package\Dependency::__set_state(array(
               'name' => 'symfony/polyfill-mbstring',
               'version' => '~1.0',
            )),
          ),
        )),
      )),
      'symfony/polyfill-ctype' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-polyfill-ctype',
         'name' => 'symfony/polyfill-ctype',
         'version' => 'v1.32.0',
         'require' => 
        array (
          'php' => '>=7.2',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/polyfill-intl-grapheme' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-polyfill-intl-grapheme',
         'name' => 'symfony/polyfill-intl-grapheme',
         'version' => 'v1.32.0',
         'require' => 
        array (
          'php' => '>=7.2',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
      'symfony/polyfill-intl-normalizer' => 
      \MXRVX\Autoloader\Composer\Package\Package::__set_state(array(
         'namespace' => 'symfony-polyfill-intl-normalizer',
         'name' => 'symfony/polyfill-intl-normalizer',
         'version' => 'v1.32.0',
         'require' => 
        array (
          'php' => '>=7.2',
        ),
         'bin' => NULL,
         'dependencies' => 
        \MXRVX\Autoloader\Composer\Package\Dependencies::__set_state(array(
           'dependencies' => 
          array (
          ),
        )),
      )),
    )
}
``` 

## Feedback

I will be glad to see your ideas, suggestions and questions in [issues](https://github.com/mxrvx/autoloader/issues).
