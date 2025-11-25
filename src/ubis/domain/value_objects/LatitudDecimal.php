<?php

namespace src\ubis\domain\value_objects;

final class LatitudDecimal
{
    private float $value;

    public function __construct(float $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(float $value): void
    {
        if ($value < -90.0 || $value > 90.0) {
            throw new \InvalidArgumentException('LatitudDecimal must be between -90 and 90');
        }
    }

    public function value(): float
    {
        return $this->value;
    }

    public static function fromNullableFloat(?float $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
