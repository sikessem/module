<?php namespace Organizer;

class Module extends Bundle {
    public function __construct(string $file, bool $required = false, bool $once = false, array $inputs = [], &...$outputs) {
        parent::__construct($file, $required, $once, $inputs, ...$outputs);
        if (!is_file($file))
            throw new Exception("No such file $file", Exception::NO_SUCH_FILE);
    }

    protected array $values = [];

    public function export(int|string $key, mixed $value): void {
        $this->values[$key] = $value;
    }

    public function import(): mixed {
        $module = $this;
        extract($this->inputs);
        $result = $this-> required ? ($this->once ? require_once $this->file : require $this->file) : ($this->once ? include_once $this->file : include $this->file);
        foreach ($this->outputs as &$output) {
            $output = array_shift($module->values);
        }
        return $result;
    }

    public function render(callable $callback = null): string {
        ob_start($callback);
        $render = (string) $this->import();
        if (!is_int($render))
            $render = ob_get_clean();
        else
            ob_end_clean();
        return $render;
    }

    public function __toString(): string {
        return $this->render();
    }
}
