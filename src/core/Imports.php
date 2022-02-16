<?php namespace Organizer;

class Imports extends Values {
    public function __construct(protected Exports $exports) {
        $this->values = $exports->values;
    }

    public function use(string ...$names): self {
        $values = [];
        foreach ($names as $name) {
            foreach ($this->exports as $key => $value) {
                if (fnmatch($name, $key)) {
                    $values[] = $value;
                }
            }
        }
        $this->values = $values;
        return $this;
    }

    public function into(string &...$vars): void {
        foreach ($vars as &$var) {
            $var = array_shift($this->values);
        }
    }
}