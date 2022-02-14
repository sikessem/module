<?php namespace Organizer;

class Exports implements \ArrayAccess, \IteratorAggregate, \Countable {
    use Values;

    public function __construct(array $values = []) {
        $this->values = $values;
    }

    public function __invoke(...$values) {
        return $this->values = $values;
    }
}