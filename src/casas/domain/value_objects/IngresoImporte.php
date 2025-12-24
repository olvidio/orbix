<?php

namespace src\casas\domain\value_objects;

final class IngresoImporte
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function equals(IngresoImporte $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableFloat(?float $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
