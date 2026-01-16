<?php

namespace src\personas\domain\value_objects;

final class SituacionCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = strtoupper($value);
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('SituacionCode cannot be empty');
        }
        // Códigos conocidos: 'A','D','E','L','T','X' ... se permite 1 carácter alfabético en mayúscula
        if (mb_strlen($value) !== 1) {
            throw new \InvalidArgumentException('SituacionCode must be exactly 1 character');
        }
        if (!preg_match('/^[A-Z]$/u', $value)) {
            throw new \InvalidArgumentException('SituacionCode must be a single uppercase letter');
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

    public function equals(SituacionCode $other): bool
    {
        return $this->value === $other->value();
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
