<?php

namespace src\actividadestudios\domain\value_objects;

final class NotaNum
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

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullable(?float $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
