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
    
    protected string $name = '';
    
    public function as(string $name): void {
        $this->name = $name;
    }
    
    public function is(string $name): bool {
        return $this->name === $name;
    }
    
    public function in(array $names): bool {
        foreach ($names as $name)
            if ($this->is($name))
                return true;
        return false;
    }
}
