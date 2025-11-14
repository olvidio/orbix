<?php

namespace src\asignaturas\domain\value_objects;

final class Creditos
{
    private float $value;

    public function __construct(float $value)
    {
        $this->validate($value);
         // normalizamos siempre a 2 decimales
        $this->value = round($value, 2);
    }

    private function validate(float $value): void
    {
         // Rango permitido: 0.00 a 5.00
        if ($value < 0 || $value > 5) {
            throw new \InvalidArgumentException('Credits must be between 0 and 5');
        }

        // No más de 2 decimales
        if (round($value, 2) !== $value) {
            throw new \InvalidArgumentException('Credits must have at most 2 decimal places');
        }
    }

    public function value(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return number_format($this->value, 2, '.', '');
    }

    public function equals(Creditos $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullable(?float $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
