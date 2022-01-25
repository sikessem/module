<?php namespace Organizer;

// Get the Composer autoload file if it exists
$composer_autoloader = null;

$root = __DIR__;
while (!isset($composer_autoloader) && dirname($root) !== $root) {
    if (is_file($composer_file = $root . DIRECTORY_SEPARATOR . 'composer.json') && is_readable($composer_file)) {
        if (($composer_data = @file_get_contents($composer_file)) && ($composer_data = @json_decode($composer_data, true))) {
            if (is_file($autoload_file = $root . DIRECTORY_SEPARATOR . ($composer_data['config']['vendor-dir'] ?? 'vendor') . DIRECTORY_SEPARATOR . 'autoload.php') && is_readable($autoload_file)) {
                $composer_autoloader = require_once $autoload_file;
            }
            unset($autoload_file);
        }
        unset($composer_data);
    }
    unset($composer_file);
    $root = dirname($root);
}
unset($root);

// Get Organizer
$organizer_autoloader = require_once __DIR__ . '/src/core/autoload.php';
spl_autoload_register($organizer_autoloader);
$organizer = new Manager(__DIR__, true, $composer_autoloader);
spl_autoload_unregister($organizer_autoloader);
unset($organizer_autoloader, $composer_autoloader);
return $organizer;
