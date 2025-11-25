<?php

namespace src\ubis\domain\value_objects;

final class CodigoPostalText
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
            throw new \InvalidArgumentException('CodigoPostalText cannot be empty');
        }
        if (mb_strlen($value) > 12) {
            throw new \InvalidArgumentException('CodigoPostalText must be at most 12 characters');
        }
        if (!preg_match("/^[A-Za-z0-9\-\s]+$/", $value)) {
            throw new \InvalidArgumentException('CodigoPostalText has invalid characters');
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
