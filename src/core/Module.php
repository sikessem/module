<?php namespace Organizer;

class Module extends Bundle {
    public function __construct(string $file, bool $required = false, bool $once = false, array $inputs = []) {
        parent::__construct($file, $required, $once, $inputs);
        if (!is_file($file))
            throw new Exception("No such file $file", Exception::NO_SUCH_FILE);
        $this->exports = new Exports();
    }

    public Exports $exports;

    public function import(): Imports {
        $module = $this;
        extract($this->inputs);
        $this->exports['default'] = $this-> required ? ($this->once ? require_once $this->file : require $this->file) : ($this->once ? include_once $this->file : include $this->file);
        return $this->imports = new Imports($this->exports);
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
