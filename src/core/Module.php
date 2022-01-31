<?php namespace Organizer;

class Module {
    public function __construct(protected string $file, protected bool $once = false) {
        if (!is_file($file))
            throw Exception("No such file $file", Exception::NO_SUCH_FILE);
    }

    protected array $vars = [];

    public function with(array $vars): self {
        foreach ($vars as $name => $value) {
            $this->vars[$name] = $value;
        }
        return $this;
    }

    public function into(&...$refs): void {
        $vals = (array) $this->import('*');
        foreach ($refs as &$ref) {
            $ref = \count($vals) > 0 ? \array_shift($vals) : null;
        }
    }

    protected array $vals = [];

    public function export(int|string $key, mixed $val, mixed ...$vals): void {
        $this->vals[$key] = empty($vals) ? $val : [$val, ...$vals];
    }

    public function import(int|string ...$patterns): mixed {
        $module = $this;
        extract($this->vars);
        $vals[] = $this->once ? require_once $this->file : require $this->file;
        foreach ($patterns as $pattern) {
            foreach ($this->vals as $key => $val) {
                if (fnmatch($pattern, $key)) {
                    $vals[$key] = $val;
                }
            }
        }
        return 1 === \count($vals) ? $vals[\array_key_first($vals)] : $vals;
    }

    public function render(callable $callback = null): string {
        ob_start($callback);
        $render = (string) $this->import();
        if (!$render)
            $render = ob_get_clean();
        else
            ob_end_clean();
        return $render;
    }

    public function __set(string $name, mixed $value): void {
        $this->export($name, $value);
    }

    public function __get(string $name): mixed {
        $this->import($name);
    }

    public function __toString(): string {
        return $this->render();
    }
}
