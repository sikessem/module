<?php namespace Organizer;

class Module extends Bundle {
    public function __construct(string $file, bool $required = false, bool $once = false, array $inputs = []) {
        parent::__construct($file, $required, $once, $inputs);
        if (!is_file($file))
            throw new Exception("No such file $file", Exception::NO_SUCH_FILE);
    }

    protected array $outputs = [];

    public function export(int|string $key, mixed $value): void {
        $this->outputs[$key] = $value;
    }

    public function import(): mixed {
        $module = $this;
        extract($this->inputs);
        $this->outputs['default'] = $this-> required ? ($this->once ? require_once $this->file : require $this->file) : ($this->once ? include_once $this->file : include $this->file);
        return \count($this->outputs) > 1 ? $this->outputs : $this->outputs['default'];
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
