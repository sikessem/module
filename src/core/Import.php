<?php namespace Organizer;

class Import {
    public function __construct(protected string $file, protected array $vars = [], protected bool $once = false) {
        if (!is_file($file))
            throw Exception("No such file $file", Exception::NO_SUCH_FILE);
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

    public function with(array $vars): self {
        foreach ($vars as $var => $val)
            $this->vars[$var] = $val;
        return $this;
    }

    public function source(): mixed {
        $module = $this;
        extract($this->vars);
        return $this->once ? require_once $this->file : require $this->file;
    }

    public function render(callable $callback = null): string {
        ob_start($callback);
        $render = (string) $this->source();
        if (!$render)
            $render = ob_get_clean();
        else
            ob_end_clean();
        return $render;
    }

    protected array $exports = [];

    public function export(mixed $value, mixed ...$values): Export {
        return $this->exports[] = new Export($value, ...$values);
    }
}
