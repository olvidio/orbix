<?php

namespace src\menus\domain\value_objects;

final class MetaMenuDescripcion
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            // allow empty? For description, empty is allowed; do not throw
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(MetaMenuDescripcion $other): bool
    {
        return $this->value === $other->value;
    }
}
