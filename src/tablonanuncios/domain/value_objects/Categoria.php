<?php

namespace src\tablonanuncios\domain\value_objects;

final class Categoria
{
    public const CAT_ALERTA = 1;
    public const CAT_AVISO = 2;

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!in_array($value, [self::CAT_ALERTA, self::CAT_AVISO], true)) {
            throw new \InvalidArgumentException('Categoria must be 1 (ALERTA) or 2 (AVISO)');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(Categoria $other): bool
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

    public function isAlerta(): bool
    {
        return $this->value === self::CAT_ALERTA;
    }

    public function isAviso(): bool
    {
        return $this->value === self::CAT_AVISO;
    }
}
