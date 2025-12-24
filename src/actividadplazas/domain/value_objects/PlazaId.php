<?php

namespace src\actividadplazas\domain\value_objects;

final class PlazaId
{
    public const PEDIDA = 1;
    public const EN_ESPERA = 2;
    public const DENEGADA = 3;
    public const ASIGNADA = 4;
    public const CONFIRMADA = 5;


    public static function getArrayPosiblesPlazas(): array
    {
        return [
            self::PEDIDA => _("pedida"),
            self::EN_ESPERA => _("en espera"),
            //self::DENEGADA => _("denegada"),
            self::ASIGNADA => _("asignada"),
            self::CONFIRMADA => _("confirmada"),
        ];
    }

    // ---------------------------------------------------------------------------
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!in_array($value, [self::PEDIDA, self::EN_ESPERA, self::DENEGADA, self::ASIGNADA, self::CONFIRMADA], true)) {
            throw new \InvalidArgumentException('PlazaId solo puede ser 1, 2, 3, 4 o 5');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(PlazaId $other): bool
    {
        return $this->value === $other->value();
    }
}
