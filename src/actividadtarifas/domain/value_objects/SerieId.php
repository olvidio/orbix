<?php

namespace src\actividadtarifas\domain\value_objects;

final class SerieId
{
    public const GENERAL = 1;
    public const ESTUDIANTE = 2;

    public static function getArraySerie(): array
    {
        return [
            self::GENERAL => _("general"),
            self::ESTUDIANTE => _("estudiante"),
        ];
    }

    //----------------------------------------------------------
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('SerieId debe ser un entero positivo');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(SerieId $other): bool
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
