<?php

namespace src\ubiscamas\domain\value_objects;

use InvalidArgumentException;

final class TipoLavabo
{

    public static function getArrayTipoLavabo(): array
    {
        return [
            1 => _("NO"),
            2 => _("completo"),
            3 => _("sin ducha"),
            4 => _("exterior"),
        ];
    }


    private int $value;

    public function __construct(int $value)
    {
        if (!array_key_exists($value, self::getArrayTipoLavabo())) {
            throw new InvalidArgumentException(sprintf('<%s> no es un valor válido para el tipo de tipoLavabo', $value));
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function getDescripcion(): string
    {
        return self::getArrayTipoLavabo()[$this->value];
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
