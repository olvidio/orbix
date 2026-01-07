<?php

namespace src\personas\domain\value_objects;

final class PersonaApellido2Text
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
            throw new \InvalidArgumentException('PersonaApellido2Text cannot be empty');
        }
        if (mb_strlen($value) > 25) {
            throw new \InvalidArgumentException('PersonaApellido2Text must be at most 25 characters');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’´\-()]+$/u", $value)) {
            throw new \InvalidArgumentException('PersonaApellido2Text has invalid characters');
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
