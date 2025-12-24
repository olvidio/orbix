<?php

namespace src\personas\domain\value_objects;

final class PersonaTratoCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('PersonaTratoCode cannot be empty');
        }
        if (mb_strlen($value) > 5) {
            throw new \InvalidArgumentException('PersonaTratoCode must be at most 5 characters');
        }
        if (!preg_match('/^[A-Za-z0-9._-]+$/u', $value)) {
            throw new \InvalidArgumentException('PersonaTratoCode has invalid characters');
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

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
