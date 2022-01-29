<?php

use Composer\Autoload\ClassLoader as ComposerAutoloader;
use Organizer\{Manager,Import};

require_once __DIR__ . '/src/core/autoload.php';

/**
 * @staticvar type $composer_autoloader              The composer autoload class loader
 * @param     string $root                           The root directory to search from
 * @return    Composer\Autoload\ClassLoader|null     The Composer autoload file if it exists
 */
function get_composer(string $root = __DIR__): ?ComposerAutoloader {
    static $composer;
    while (!isset($composer) && dirname($root) !== $root) {
        if (is_file($composer_file = $root . DIRECTORY_SEPARATOR . 'composer.json') && is_readable($composer_file)) {
            if (($composer_data = @file_get_contents($composer_file)) && ($composer_data = @json_decode($composer_data, true))) {
                if (is_file($autoload_file = $root . DIRECTORY_SEPARATOR . ($composer_data['config']['vendor-dir'] ?? 'vendor') . DIRECTORY_SEPARATOR . 'autoload.php') && is_readable($autoload_file)) {
                    $composer = require_once $autoload_file;
                }
                unset($autoload_file);
            }
            unset($composer_data);
        }
        unset($composer_file);
        $root = dirname($root);
    }
    return $composer;
}

function get_organizer(string $root = __DIR__): Manager {
    static $organizer;
    if (!isset($organizer))
        $organizer = new Manager($root, true, ['.php'], get_composer());
    return $organizer;
}

if (!function_exists('import')) {
    function import(string $name, array $vars = [], bool $once = true): Import {
        return get_organizer()->import($name, $vars, $once);
    }
}

return get_organizer();
