<?php namespace Organizer;

trait Values {
    public array $values = [];

    public function offsetExists($offset): bool {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset): mixed {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value): void {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->values[$offset]);
    }

    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->values);
    }

    public function count(): int {
        return \count($this->values);
    }

    public function __invoke(): mixed {
        return $this->values['default']();
    }

    public function __debugInfo(): array {
        return $this->values;
    }

    public function __get($name): mixed {
        return $this->values[$name];
    }

    public function __set($name, $value): void {
        $this->values[$name] = $value;
    }

    public function __isset($name): bool {
        return isset($this->values[$name]);
    }

    public function __unset($name): void {
        unset($this->values[$name]);
    }

    public function __call($name, $arguments): mixed {
        return $this->values[$name](...$arguments);
    }
}