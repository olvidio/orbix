<?php

namespace src\actividadtarifas\domain\value_objects;

final class TarifaId
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
            throw new \InvalidArgumentException('TarifaId debe ser un entero positivo');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(TarifaId $other): bool
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
