<?php

namespace src\menus\domain\value_objects;

final class MenuParametros
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        // Allow empty string, but trim spaces
        // Add specific validation if needed
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(MenuParametros $other): bool
    {
        return $this->value === $other->value;
    }
}
