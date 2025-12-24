<?php

namespace src\cambios\domain\value_objects;

final class AvisoTipoId
{
    public const LISTA = 1;
    public const MAIL = 2;


    public static function getArrayAvisoTipo(): array
    {
        return [
            self::LISTA => _("lista"),
            self::MAIL => _("email"),
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
        if (!in_array($value, [self::LISTA, self::MAIL], true)) {
            throw new \InvalidArgumentException('AvisoTipoId solo puede ser 1 o 2');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(AvisoTipoId $other): bool
    {
        return $this->value === $other->value();
    }
}
