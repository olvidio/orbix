<?php

namespace src\ubis\domain\value_objects;

final class DlCode
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
            throw new \InvalidArgumentException('DlCode cannot be empty');
        }
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('DlCode must be at most 20 characters');
        }
        // Códigos alfanuméricos cortos con separadores comunes
        if (!preg_match("/^[A-Za-z0-9._\-]+$/u", $value)) {
            throw new \InvalidArgumentException('DlCode has invalid characters');
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

    public function equals(DlCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
