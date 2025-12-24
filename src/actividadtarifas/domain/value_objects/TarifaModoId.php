<?php

namespace src\actividadtarifas\domain\value_objects;

final class TarifaModoId
{
    public const POR_DIA = 0;
    public const TOTAL = 1;

    public static function getArrayModo(): array
    {
        return [
            self::TOTAL => _("total"),
            self::POR_DIA => _("por día"),
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
        if (!array_key_exists($value, self::getArrayModo())) {
            throw new \InvalidArgumentException(sprintf('El valor %s no es válido para TarifaModoId', $value));
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(TarifaModoId $other): bool
    {
        return $this->value === $other->value();
    }
}
