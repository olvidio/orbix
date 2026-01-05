<?php

namespace src\ubis\domain\value_objects;

/**
 * Value Object para la cantidad de una tarifa
 */
final class TarifaCantidad
{
    private float $value;

    public function __construct(float $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(float $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('TarifaCantidad cannot be negative');
        }
    }

    public function value(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(TarifaCantidad $other): bool
    {
        return $this->value === $other->value();
    }
}
