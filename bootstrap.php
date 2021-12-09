<?php

function root(?string $path = null): ?string {
    static $root;
    if (isset($path)) {
        if (!\is_dir($path))
            throw new \InvalidArgumentException("$path is not a directory");

        $_root = $root;
        $root = \realpath($path) . DIRECTORY_SEPARATOR;
        return $_root;
    }

    if (!isset($root))
        throw new \RuntimeException("root() is not set");

    return $root;
}

function organize(string $root): array {
    root($root);
    $organizer_data = [];
    if (\is_readable($organizer_file = root() . 'organizer.json')) {
        $organizer_data = \json_decode(
            \file_get_contents($organizer_file),
            true
        );

        if (\json_last_error() > 0) {
            \fprintf(
                STDERR,
                '%s in %s' . PHP_EOL,
                \json_last_error_msg(),
                $organizer_file
            );
            exit(1);
        }
    }
    return $organizer_data;
}