<?php

namespace src\actividadcargos\domain\value_objects;

final class OrdenCargo
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
            throw new \InvalidArgumentException('OrdenCargo must be a non-negative integer');
        }
        // Máximo 8 dígitos según UI (DatosCampo->setArgument(8))
        if ($value > 99999999) {
            throw new \InvalidArgumentException('OrdenCargo must be at most 8 digits');
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

    public function equals(OrdenCargo $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
