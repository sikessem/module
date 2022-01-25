<?php namespace Organizer;

use Composer\Autoload\ClassLoader as ComposerAutoloader;

class Manager {
    public function __construct($path, bool $prepend = false, ?ComposerAutoloader $autoloader = null) {
        $this->organize($path, $prepend);
        if (isset($autoloader))
            $this->setComposerAutoloader($autoloader);
    }
    
    protected static array $ORGANIZED = [];
    
    protected ?Plateform $plateform = null;

    public function organize($path, bool $prepend = false): static {
        $this->addPath($path, $prepend);
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
        return implode(PATH_SEPARATOR, $this->getPath());
    }
}
