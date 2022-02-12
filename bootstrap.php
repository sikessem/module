<?php

use Composer\Autoload\ClassLoader as ComposerAutoloader;
use Organizer\Manager;

require_once __DIR__ . '/src/core/autoload.php';

function get_organizer(string $root = __DIR__): Manager {
    static $organizer;
    if (!isset($organizer))
        $organizer = new Manager($root, true, ['.php']);
    return $organizer;
}

if (!function_exists('import')) {
    function import(string $name, bool $required = false, bool $once = true, array $inputs = [], &...$outputs): mixed {
        return get_organizer()->import($name, $required, $once, $inputs, ...$outputs);
    }
}

if (!function_exists('organize')) {
    function organize(string $path, bool $prepend = true, array $extensions = []): Manager {
        return get_organizer()->organize($path, $prepend, $extensions);
    }
}

if (!function_exists('compose')) {
    function organize(string $root): ?ComposerAutoloader {
        return Manager::compose($root);
    }
}

return get_organizer();
