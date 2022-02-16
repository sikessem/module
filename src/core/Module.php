<?php namespace Organizer;

class Module {
    public function __construct(protected string $file) {
        if (!is_file($file))
            throw new Exception("No such file $file", Exception::NO_SUCH_FILE);
        if (!is_readable($file))
            throw Exception("$file is not readable", Exception::FILE_NOT_READABLE);
    }

    protected array $inputs = [];

    public function with(string|array $inputs, mixed $value = null): self {
        if (is_string($inputs))
            $this->inputs[$inputs] = $value;
        elseif (isset($value)) {
            foreach ($inputs as $input)
                $this->with($input, $value);
        }
        else foreach ($inputs as $input => $value)
            $this->with($input, $value);
        return $this;
    }

    protected bool $once = false;

    public function once(): self {
        $this->once = true;
        return $this;
    }

    protected bool $required = false;

    public function required(): self {
        $this->required = true;
        return $this;
    }

    protected array $outputs = [];

    public function export(string|array $outputs, mixed $value = null): self {
        if (is_string($outputs))
            $this->outputs[$outputs] = $value;
        elseif (isset($value)) {
            foreach ($outputs as $output)
                $this->export($output, $value);
        }
        else foreach ($outputs as $output => $value)
            $this->export($output, $value);
        return $this;
    }

    public function import(): Values {
        extract($this->inputs);
        $this->outputs['default'] = $this->required ? ($this->once ? require_once $this->file : require $this->file) : ($this->once ? include_once $this->file : include $this->file);
        return new Values($this->outputs);
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
