<?php

namespace src\cambios\domain\value_objects;

final class TipoCambioId
{
    public const INSERT = 1;
    public const UPDATE = 2;
    public const DELETE = 3;
    public const FASE = 4;


    public static function getArrayTipoCambio(): array
    {
        return [
            self::INSERT => _("insert"),
            self::UPDATE => _("update"),
            self::DELETE => _("delete"),
            self::FASE => _("fase"),
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
        if (!in_array($value, [self::INSERT, self::UPDATE, self::DELETE, self::FASE], true)) {
            throw new \InvalidArgumentException('TipoCambioId solo puede ser 1, 2, 3 o 4');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(TipoCambioId $other): bool
    {
        return $this->value === $other->value();
    }
}
