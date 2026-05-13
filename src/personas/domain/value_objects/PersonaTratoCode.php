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
        if (!preg_match("/^[\p{L}\p{M}\p{N}\p{P}\p{S}\p{Z}]+$/u", $value)) {
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
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
