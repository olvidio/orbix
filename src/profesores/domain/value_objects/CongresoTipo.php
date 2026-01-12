<?php

namespace src\profesores\domain\value_objects;

final class CongresoTipo
{
    public const CV = 1;
    public const CONGRESO = 2;
    public const REUNION = 3;
    public const CLAUSTRO = 4;

    public static function getArrayTiposCongreso(): array
    {
        $tipos_congreso = [
            self::CV => _("cv"),
            self::CONGRESO => _("congreso"),
            self::REUNION => _("reunión"),
            self::CLAUSTRO => _("claustro"),
        ];

        return $tipos_congreso;
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
        if (!in_array($value, [self::CV, self::CONGRESO, self::REUNION, self::CLAUSTRO], true)) {
            throw new \InvalidArgumentException('TipoCongreso solo puede ser 1, 2, 3 o 4');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(CongresoTipo $other): bool
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
