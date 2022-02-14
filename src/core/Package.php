<?php namespace Organizer;

class Package extends Bundle {
    public function __construct(string $file, bool $required = false, bool $once = false, array $inputs = []) {
        parent::__construct($file, $required, $once, $inputs);
        if (!is_dir($file))
            throw new Exception("No such directory $file", Exception::NO_SUCH_FILE);
    }
    
    public function import(): mixed {
        $dir = new \DirectoryIterator($this->file);
        $results = [];
        foreach ($dir as $file) {
            if ($file->isFile()) {
                $module = new Module($file->getPathname(), $this->required, $this->once, $this->inputs);
                $results[] = $module->import();
            }
            elseif (!$file->isDot()) {
                $package = new Package($file->getPathname(), $this->required, $this->once, $this->inputs);
                $results[] = $package->import();
            }
        }
        return 1 === \count($results) ? $results[\array_key_first($results)] : $results;
    }
}