<?php

namespace src\inventario\domain\value_objects;

final class UbiInventarioName
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
            throw new \InvalidArgumentException('UbiInventarioName cannot be empty');
        }
        if (mb_strlen($value) > 30) {
            throw new \InvalidArgumentException('UbiInventarioName must be at most 30 characters');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’\-()]+$/u", $value)) {
            throw new \InvalidArgumentException('UbiInventarioName has invalid characters');
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

    public function equals(UbiInventarioName $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
