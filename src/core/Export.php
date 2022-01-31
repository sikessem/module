<?php namespace Organizer;

class Export {
    public function __construct(mixed $value, mixed ...$values) {
        $this->addValue($value);
        $this->addValues($values);
    }
    
    protected array $values = [];
    
    public function addValue(mixed $value): void {
        if (!in_array($value, $this->values, true))
            $this->values[] = $value;
    }
    
    public function addValues(array $values): void {
        foreach ($values as $value)
            $this->addValue($value);
    }
    
    public function getValues(): mixed {
        return count($this->values) > 1 ? $this->values : $this->values[0];
    }
    
    protected string $alias = '';
    
    public function as(string $alias): void {
        $this->alias = $alias;
    }
    
    public function is(string $pattern): bool {
        return fnmatch($pattern, $this->alias);
    }

    public function in(array $patterns): bool {
        foreach ($patterns as $pattern)
            if ($this->is($pattern))
                return true;
        return false;
    }
}
