<?php namespace Organizer;

use Composer\Autoload\ClassLoader as ComposerAutoloader;

class Organizer {
    public static function organize(string|array $path, array $extensions = [], bool $prepend = true): void {
        self::addPath($path, $prepend);
        self::addExtensions($extensions);
        self::$COMPOSER_AUTOLOADERS[$path] = self::getComposerAutoloader($path);
    }

    protected static array $COMPOSER_AUTOLOADERS = [];
    
    public static function getComposerAutoloader(string $root = __DIR__): ?ComposerAutoloader {
        while (!isset(self::$COMPOSER_AUTOLOADERS[$root]) && dirname($root) !== $root) {
            if (is_file($composer_file = $root . DIRECTORY_SEPARATOR . 'composer.json') && is_readable($composer_file)) {
                if (($composer_data = @file_get_contents($composer_file)) && ($composer_data = @json_decode($composer_data, true))) {
                    if (is_file($autoload_file = $root . DIRECTORY_SEPARATOR . ($composer_data['config']['vendor-dir'] ?? 'vendor') . DIRECTORY_SEPARATOR . 'autoload.php') && is_readable($autoload_file)) {
                        $COMPOSER_AUTOLOADERS[$root] = require_once $autoload_file;
                    }
                }
            }
            $root = dirname($root);
        }
        return $COMPOSER_AUTOLOADERS[$root] ?? null;
    }

    protected static string $PATH = '';
    
    public static function addPath(string|array $path, bool $prepend = false): void {
        if (is_array($path)) {
            foreach ($path as $p) {
                self::addPath($p, $prepend);
            }
            return;
        }

        if (!\is_dir($path))
            throw new Exception("No such directory $path", Exception::UNKNOWN_PATH);

        if (!\is_readable($path))
            throw new Exception("Cannot read directory $path", Exception::NOT_READABLE);
        
        $path = \realpath($path) . \DIRECTORY_SEPARATOR;

        if (empty(self::$PATH)) {
            self::setPath($path);
            return;
        }
        
        if (in_array($path, self::getPathList(), true))
            return;

        $path = $prepend ? $path . \PATH_SEPARATOR . self::getPath() : self::getPath() . \PATH_SEPARATOR . $path;
        self::setPath($path);
    }
    
    public static function setPath(string $path): void {
        self::$PATH = $path;
    }
    
    public static function getPath(): string {
        return self::$PATH;
    }
    
    public static function setPathList(array $list): void {
        self::setPath(implode(\PATH_SEPARATOR, $list));
    }
    
    public static function getPathList(): array {
        return explode(\PATH_SEPARATOR, self::getPath());
    }
    
    protected static array $EXT = [];
    
    public static function addExtensions(array $extensions): void {
        foreach ($extensions as $extension)
            self::addExtension($extension);
    }
    
    public static function addExtension(string $extension): void {
        $extension = strtolower($extension);
        if (!in_array($extension, self::getExtensions(), true))
            self::$EXT[] = $extension;
    }
    
    public static function getExtensions(): array {
        return self::$EXT;
    }

    protected static array $FILES = [];

    public static function getPathOf(string $name): ?string {
        if (!isset(self::$FILES[$name])) {
            if (preg_match('/[\/:*?"<>|]/U', $name))
                throw new Exception("Invalid name $name given", Exception::INVALID_NAME);
            $path = $name;
            while (is_int($sepos = strpos($path, '.'))) {
                $path = substr_replace($path, DIRECTORY_SEPARATOR, $sepos, 1);
                foreach (self::getPathList() as $dir) {
                    if (file_exists($file = $dir . $path))
                        return self::$FILES[$name] = $file;
                    foreach (self::getExtensions() as $extension) {
                        if (file_exists($file = $dir . $path . $extension))
                            return self::$FILES[$name] = $file;
                    }
                }
            }
        }
        return self::$FILES[$name] ?? null;
    }

    protected static array $BUNDLES = [];

    public static function import(string $name, bool $required, bool $once = false, array $inputs = []): mixed {
        if (!isset(self::$BUNDLES[$name])) {
            if ($file = self::getPathOf($name))
                self::$BUNDLES[$name] = (is_dir($file) ? new Package($file, $required, $once, $inputs) : new Module($file, $required, $once, $inputs))->import();
            else throw new Exception("No module named $name exists", Exception::UNKNOWN_PATH);
        }
        return self::$BUNDLES[$name];
    }
}
