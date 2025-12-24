<?php

namespace src\menus\domain\value_objects;

final class MetaMenuUrl
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
            throw new \InvalidArgumentException('MetaMenuUrl cannot be empty');
        }
        // Could add URL format checks if needed
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(MetaMenuUrl $other): bool
    {
        return $this->value === $other->value;
    }
}
