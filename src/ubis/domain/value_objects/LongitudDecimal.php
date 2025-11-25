<?php

namespace src\ubis\domain\value_objects;

final class LongitudDecimal
{
    private float $value;

    public function __construct(float $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(float $value): void
    {
        if ($value < -180.0 || $value > 180.0) {
            throw new \InvalidArgumentException('LongitudDecimal must be between -180 and 180');
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
