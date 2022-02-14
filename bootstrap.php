<?php

use Composer\Autoload\ClassLoader as ComposerAutoloader;
use Organizer\Manager;

require_once __DIR__ . '/src/core/autoload.php';

if (!function_exists('import')) {
    function import(string $name, bool $required = false, bool $once = true, array $inputs = []): mixed {
        return Manager::import($name, $required, $once, $inputs);
    }
}

if (!function_exists('organize')) {
    function organize(string $root, bool $prepend = true, array $extensions = []): void {
        Manager::organize($root, $prepend, $extensions);
    }
}

if (!function_exists('compose')) {
    function compose(string $root): ?ComposerAutoloader {
        return Manager::compose($root);
    }
}
