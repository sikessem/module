<?php namespace Organizer;

use Composer\Autoload\ClassLoader as ComposerAutoloader;

class Manager {
    public function __construct(string $path, bool $prepend = false, array $extensions = [], ?ComposerAutoloader $autoloader = null) {
        $this->organize($path, $prepend, $extensions);
        if (isset($autoloader))
            $this->setComposerAutoloader($autoloader);
    }
    
    protected static array $ORGANIZED = [];
    
    protected ?Plateform $plateform = null;

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
        if (!in_array($extension, $$this->extensions))
            $this->extensions[] = $extension;
    }
    
    public function getExtensions(): array {
        return $this->extensions;
    }

    /**
     * Modules extensions list
     */
    const EXTENSIONS = [
        '.php',
        '.m.php',
        '.mod.php',
        '.module.php',
    ];
    
    protected array $import = [];

    /**
     * @param string $name The module name
     * @param bool $once The module is it once ?
     * @param array $vars The module required vars
     * @return mixed The module returned value
     */
    public function import(string $name, array $vars = [], bool $once = true): static {
        if(preg_match('/[\/:*?"<>|]/U', $name))
            throw new Error("Invalid name $name given", Error::INVALID_PATTERN);

        foreach (array_reverse($this->getPathList()) as $dir) {
            $source_found = false;
            foreach($this->getExtensions() ?: self::EXTENSIONS as $extension) {
                if (!is_file($file = $dir . $name)) {
                    $path = $name;
                    while(!is_file($file = $dir . $path . $extension) && is_int($sepos = strpos($path, '.')))
                        $path = substr_replace($path, DIRECTORY_SEPARATOR, $sepos, 1);
                }
                if(is_readable($file)) {
                    $this->import['file'] = $file;
                    $this->import['vars'] = $vars;
                    $this->import['once'] = $once;
                    $source_found = true;
                    break;
                }
            }
            if ($source_found) break;
        }
        return $this;
    }
    
    public function use(string ...$names): mixed {
        $organizer = $module = $this;
        if (empty($this->import))
            throw new \RuntimeException('Nothing to import');
        extract($this->import['vars']);
        $return_value = $this->import['once'] ? require_once $this->import['file'] : require $this->import['file'];
        $this->import = [];
        if (!empty($names)) {
            if (count($names) === 1) {
                $name = implode('', $names);
                $return_value = $name === '*' ? $this->export : $this->export[$name] ?? null;
            }
            else {
                $return_value = [];
                foreach ($names as $name)
                    $return_value[$name] = $this->export[$name] ?? null;
            }
        }
        $this->export = [];
        return $return_value;
    }
    
    protected array $export = [];

    public function export(mixed $value, mixed ...$values): static {
        $this->export[] = empty($values) ? $value : [$value, ...$values];
        return $this;
    }
    
    public function as(string $name): void {
        if (empty($this->export))
            throw new \RuntimeException('Nothing to export');
        $key = array_key_last($this->export);
        $this->export[$name] = $this->export[$key];
        unset($this->export[$key]);
    }
}
