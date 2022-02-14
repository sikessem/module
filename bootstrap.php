<?php

use Organizer\Organizer;

require_once __DIR__ . '/src/core/autoload.php';

if (!function_exists('import')) {
    function import(string $name, bool $required = false, bool $once = true, array $inputs = []): mixed {
        return Organizer::import($name, $required, $once, $inputs);
    }
}

if (!function_exists('organize')) {
    function organize(string $root, array $extensions = [], bool $prepend = true): void {
        Organizer::organize($root, $extensions, $prepend);
    }
}