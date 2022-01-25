<?php namespace Organizer;

use Composer\Autoload\ClassLoader as ComposerAutoloader;

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
    if (!isset($organizer)) {
        $organizer_autoloader = require_once __DIR__ . '/src/core/autoload.php';
        spl_autoload_register($organizer_autoloader);
        $organizer = new Manager($root, true, get_composer());
        spl_autoload_unregister($organizer_autoloader);
    }
    return $organizer;
}

return get_organizer();
