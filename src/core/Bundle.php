<?php namespace Organizer;

abstract class Bundle {
    public function __construct(protected string $file, protected bool $required = false, protected bool $once = false, protected array $inputs = [], &...$outputs) {
        if ($required && !is_readable($file))
            throw Exception("File $file is not readable", Exception::FILE_NOT_READABLE);
        
        foreach ($outputs as &$output) {
            $this->outputs[] = &$output;
        }
    }

    protected array $outputs = [];

    abstract public function import(): mixed;
}