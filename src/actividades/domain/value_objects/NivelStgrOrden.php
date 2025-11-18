<?php

namespace src\actividades\domain\value_objects;

final class NivelStgrOrden
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
            throw new \InvalidArgumentException('NivelStgrOrden must be a non-negative integer');
        }
        // UI: argument(3) sugiere hasta 3 dígitos
        if ($value > 999) {
            throw new \InvalidArgumentException('NivelStgrOrden must be at most 3 digits');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(NivelStgrOrden $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
