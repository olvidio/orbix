<?php

namespace src\ubis\domain\value_objects;

final class DescTelecoOrder
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('DescTelecoOrder must be a non-negative integer');
        }
        // UI indica argumento 2 (máx 2 dígitos)
        if ($value > 99) {
            throw new \InvalidArgumentException('DescTelecoOrder must be at most 2 digits (0-99)');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(DescTelecoOrder $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
