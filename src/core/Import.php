<?php namespace Organizer;

class Import {
    public function __construct(protected string $file, protected bool $once = false) {
        if (!is_file($file))
            throw Exception("No such file $file", Exception::NO_SUCH_FILE);
    }

    protected array $exports = [];

    public function export(mixed $val, mixed ...$vals): Export {
        return $this->exports[] = new Export($val, ...$vals);
    }

    protected array $vars = [];

    public function with(array $vars): self {
        foreach ($vars as $var => $val) {
            $this->vars[$var] = $val;
        }
        return $this;
    }

    public function use(string $var, string ...$vars): mixed {
        $val = $this->source();
        if (empty($vars)) {
            if ($var === 'default') {
                return $val;
            }
            else {
                $vals = [];
                foreach ($this->exports as $export) {
                    if ($export->is($var)) {
                        $vals[] = $export->getValues();
                    }
                }
                $vals['default'] = $val;
                return \count($vals) > 1 ? $vals : $vals[\array_key_first($vals)];
            }
        }
        else {
            $vals = [];
            $vars = [$var, ...$vars];
            foreach ($exports as $export) {
                if ($export->in($vars)) {
                    $vals[] = $export->getValues();
                }
            }
            $vals['default'] = $val;
            return $vals;
        }
    }

    public function into(&$ref, &...$refs): void {
        $refs = [&$ref, ...$refs];
        $vals = (array) $this->use('*');
        foreach ($refs as &$ref) {
            $ref = \count($vals) > 0 ? \array_shift($vals) : null;
        }
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
}
