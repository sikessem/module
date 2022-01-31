<?php namespace Organizer;

class Import {
    public function __construct(protected string $file, protected array $vars = [], protected bool $once = false) {
        if (!is_file($file))
            throw new \InvalidArgumentException("No such file $file");
    }
    
    public function use(string ...$names): mixed {
        $result = $this->source();
        if (!empty($names)) {
            if (count($names) === 1) {
                $name = $names[0];
                if ($name === '*') {
                    $result = [];
                    foreach ($this->exports as $export) {
                        $result[] = $export->getValues();
                    }
                }
                else {
                    foreach ($this->exports as $export) {
                        if ($export->is($name))
                            return $export->getValues();
                    }
                }
            }
            else {
                $result = [];
                foreach ($exports as $export)
                    if ($export->in($names))
                        $result[] = $export->getValues();
            }
        }
        return $result;
    }

    public function source(): mixed {
        $module = $this;
        extract($this->vars);
        return $this->once ? require_once $this->file : require $this->file;
    }

    protected array $exports = [];

    public function export(mixed $value, mixed ...$values): Export {
        return $this->exports[] = new Export($value, ...$values);
    }
}
