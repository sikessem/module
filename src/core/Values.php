<?php namespace Organizer;

class Values {
    public function __construct(array $items) {
        $this->setList($items);
    }

    protected array $items = [];

    public function use(string ...$names): self {
        $items = [];
        foreach ($names as $name) {
            foreach ($this->items as $key => $value) {
                if (fnmatch($name, $key)) {
                    $items[$key] = $value;
                }
            }
        }
        return new self($items);
    }

    public function as(&...$vars): void {
        foreach ($vars as &$var) {
            $var = array_shift($this->items);
        }
    }

    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->items);
    }

    public function count(): int {
        return \count($this->items);
    }

    public function __debugInfo(): array {
        return $this->items;
    }

    public function setList(array $items): void {
        foreach ($items as $key => $value)
            $this->setItem($key, $value);
    }

    public function getList(): array {
        return $this->items;
    }

    public function setItem(int|string $key, mixed $value): void {
        $this->items[$key] = $value;
    }

    public function getValue(int|string $key): mixed {
        return $this->items[$key] ?? null;
    }

    public function getKey(mixed $value, bool $strict = true): int|string|null {
        return false === ($key = array_search($value, $this->items, $strict)) ? null : $key;
    }

    public function keyExists(int|string $key): bool {
        return array_key_exists($key, $this->items);
    }

    public function valueExists(mixed $value, bool $strict = true): bool {
        return in_array($value, $this->items, $strict);
    }

    public function removeKey(int|string $key): void {
        unset($this->items[$key]);
    }

    public function removeValue(mixed $value, bool $strict = true): void {
        $key = $this->getKey($value, $strict);
        if (isset($key))
            array_splice($this->items, $key, 1);
    }

    public function offsetExists($offset): bool {
        return $this->keyExists($offset);
    }

    public function offsetGet($offset): mixed {
        return $this->getValue($offset);
    }

    public function offsetSet($offset, $value): void {
        $this->setItem($offset, $value);
    }

    public function offsetUnset($offset): void {
        $this->removeKey($offset);
    }

    public function __get($name): mixed {
        return $this->getValue($name);
    }

    public function __set($name, $value): void {
        $this->setItem($name, $value);
    }

    public function __isset($name): bool {
        return $this->keyExists($name);
    }

    public function __unset($name): void {
        $this->removeKey($name);
    }

    public function __invoke(...$args): self {
        return $this->__call('default', $args);
    }

    public function __call($name, $params): mixed {
        $value = $this->getValue($name);
        if (!is_callable($value))
            throw new \BadMethodCallException("Method '$name' does not exist");
        return $value(...$params);
    }
}