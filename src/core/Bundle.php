<?php namespace Organizer;

abstract class Bundle {
    public function __construct(protected string $file, protected bool $required = false, protected bool $once = false, protected array $inputs = []) {
        if ($required && !is_readable($file))
            throw Exception("File $file is not readable", Exception::FILE_NOT_READABLE);
    }

    abstract public function import(): mixed;
}