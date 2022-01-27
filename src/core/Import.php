<?php namespace Organizer;

class Import {
    public function __construct(protected string $file, protected array $vars = [], protected bool $once = false) {
        if (!is_file($file))
            throw new \InvalidArgumentException("No such file $file");
    }
    
    public function use(string ...$names): mixed {
        $module = $this;
        extract($this->vars);
        $result = $this->once ? require_once $this->file : require $this->file;
        if (!empty($names)) {
            if (count($names) === 1) {
                $name = implode('', $names);
                $result = $name === '*' ? $this->values : $this->values[$name] ?? null;
            }
            else {
                $result = [];
                foreach ($names as $name)
                    $result[$name] = $this->values[$name] ?? null;
            }
        }
        return $result;
    }
    
    protected array $values = [];

    public function export(mixed $value, mixed ...$values): static {
        $this->values[] = empty($values) ? $value : [$value, ...$values];
        return $this;
    }
    
    public function as(string $name): void {
        if (empty($this->export))
            throw new \RuntimeException('Nothing to export');
        $key = array_key_last($this->export);
        $this->values[$name] = $this->export[$key];
        unset($this->values[$key]);
    }
}
