<?php

namespace src\casas\domain\value_objects;

final class UbiGastoTipo
{
    public const APORTACION_SV = 1; // aportación sv.
    public const APORTACION_SF = 2; // aportación sf.
    public const GASTO = 3; // gastos.

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        $allowedValues = [
            self::APORTACION_SV,
            self::APORTACION_SF,
            self::GASTO,
        ];

        if (!in_array($value, $allowedValues, true)) {
            throw new \InvalidArgumentException(sprintf('"%s" no es un tipo de gasto válido', $value));
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(UbiGastoTipo $other): bool
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
