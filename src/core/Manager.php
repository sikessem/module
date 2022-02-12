<?php namespace Organizer;

use Composer\Autoload\ClassLoader as ComposerAutoloader;

class Manager {
    public function __construct(string $path, bool $prepend = false, array $extensions = []) {
        $this->organize($path, $prepend, $extensions);
    }

    public function organize(string $path, bool $prepend = true, array $extensions = []): static {
        $this->addPath($path, $prepend);
        $this->addExtensions($extensions);
        self::compose($path);
        return $this;
    }

    protected static array $COMPOSER_AUTOLOADERS = [];
    
    public static function compose(string $root = __DIR__): ?ComposerAutoloader {
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

    protected string $path = '';
    
    public function addPath(string $path, bool $prepend = false): void {
        if (!\is_dir($path))
            throw new Exception("No such directory $path", Exception::UNKNOWN_PATH);

        if (!\is_readable($path))
            throw new Exception("Cannot read directory $path", Exception::NOT_READABLE);
        
        $path = \realpath($path) . \DIRECTORY_SEPARATOR;

        if (empty($this->path)) {
            $this->setPath($path);
            return;
        }
        
        if (in_array($path, $this->getPathList(), true))
            return;

        $path = $prepend ? $path . \PATH_SEPARATOR . $this->getPath() : $this->getPath() . \PATH_SEPARATOR . $path;
        $this->setPath($path);
    }
    
    public function setPath(string $path): void {
        $this->path = $path;
    }
    
    public function getPath(): string {
        return $this->path;
    }
    
    public function setPathList(array $list): void {
        $this->path = '';
        foreach ($list as $path)
            $this->addPath ($path);
    }
    
    public function getPathList(): array {
        return explode(PATH_SEPARATOR, $this->getPath());
    }
    
    protected array $extensions = [];
    
    public function addExtensions(array $extensions): void {
        foreach ($extensions as $extension)
            $this->addExtension($extension);
    }
    
    public function addExtension(string $extension): void {
        $extension = strtolower($extension);
        if (!in_array($extension, $this->extensions))
            $this->extensions[] = $extension;
    }
    
    public function getExtensions(): array {
        return $this->extensions;
    }

    protected array $pathnames = [];

    public function getPathOf(string $name): ?string {
        if (!isset($this->pathnames[$name])) {
            if (preg_match('/[\/:*?"<>|]/U', $name))
                throw new Exception("Invalid name $name given", Exception::INVALID_NAME);
            $path = $name;
            while (is_int($sepos = strpos($path, '.'))) {
                $path = substr_replace($path, DIRECTORY_SEPARATOR, $sepos, 1);
                foreach ($this->getPathList() as $dir) {
                    if (file_exists($file = $dir . $path))
                        return $this->pathnames[$name] = $file;
                    foreach ($this->getExtensions() as $extension) {
                        if (file_exists($file = $dir . $path . $extension))
                            return $this->pathnames[$name] = $file;
                    }
                }
            }
        }
        return $this->pathnames[$name] ?? null;
    }

    protected array $bundles = [];

    public function import(string $name, bool $required, bool $once = false, array $inputs = [], &...$outputs): Module {
        if (!isset($this->bundles[$name])) {
            if ($file = $this->getPathOf($name))
                $this->bundles[$name] = (is_dir($file) ? new Package($file, $required, $once, $inputs, ...$outputs) : new Module($file, $required, $once, $inputs, ...$outputs))->import();
            else throw new Exception("No module named $name exists", Exception::UNKNOWN_PATH);
        }
        return $this->bundles[$name];
    }
}
