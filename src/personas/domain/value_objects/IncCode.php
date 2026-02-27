<?php

namespace src\personas\domain\value_objects;

final class IncCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = strtolower($value);
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('IncCode cannot be empty');
        }
        if (mb_strlen($value) > 2) {
            throw new \InvalidArgumentException('IncCode must be at most 2 characters');
        }
        // aceptar interrogante '?'
        if ($value !== '?' && !preg_match('/^[A-Za-z0-9]{1,2}$/', $value)) {
            throw new \InvalidArgumentException('IncCode has invalid characters');
        }
    }

    public function value(): string { return $this->value; }
    public function __toString(): string { return $this->value; }

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
