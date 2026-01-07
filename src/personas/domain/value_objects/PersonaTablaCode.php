<?php

namespace src\personas\domain\value_objects;

final class PersonaTablaCode
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
            throw new \InvalidArgumentException('PersonaTablaCode cannot be empty');
        }
        if (mb_strlen($value) > 6) {
            throw new \InvalidArgumentException('PersonaTablaCode must be at most 6 characters');
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $value)) {
            throw new \InvalidArgumentException('PersonaTablaCode has invalid characters');
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

    public static function fromString(string $value): self
    {
        return new self($value);
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
