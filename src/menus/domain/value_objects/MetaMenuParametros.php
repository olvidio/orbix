<?php

namespace src\menus\domain\value_objects;

final class MetaMenuParametros
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        // Allow empty string; add format checks if needed
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(MetaMenuParametros $other): bool
    {
        return $this->value === $other->value;
    }
}
