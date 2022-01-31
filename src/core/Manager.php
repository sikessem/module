<?php namespace Organizer;

use Composer\Autoload\ClassLoader as ComposerAutoloader;

class Manager {
    public function __construct(string $path, bool $prepend = false, array $extensions = [], ?ComposerAutoloader $autoloader = null) {
        $this->organize($path, $prepend, $extensions);
        if (isset($autoloader))
            $this->setComposerAutoloader($autoloader);
    }

    public function organize(string $path, bool $prepend = true, array $extensions = []): static {
        $this->addPath($path, $prepend);
        $this->addExtensions($extensions);
        return $this;
    }
    
    protected ?ComposerAutoloader $composer_autoloader = null;
    
    public function setComposerAutoloader(ComposerAutoloader $autoloader): void {
        $this->composer_autoloader = $autoloader;
    }
    
    public function getComposerAutoloader(): ?ComposerAutoloader {
        return $this->composer_autoloader;
    }

    protected string $path = '';
    
    public function addPath(string $path, bool $prepend = false): void {
        if (!\is_dir($path))
            throw new \InvalidArgumentException("No such directory $path");

        if (!\is_readable($path))
            throw new \InvalidArgumentException("Cannot read directory $path");
        
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
                throw new \InvalidArgumentException("Invalid name $name given");
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

    protected array $imports = [];

    public function import(string $name, array $vars = [], bool $once = true): Import {
        if (!isset($this->imports[$name])) {
            if ($file = $this->getPathOf($name))
                $this->imports[$name] = new Import($file, $vars, $once);
            else throw new \RuntimeException("No module named $name exists");
        }
        return $this->imports[$name];
    }
}
