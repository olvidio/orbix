<?php

declare(strict_types=1);

namespace src\actividades\domain\value_objects;

/**
 * Identificador de tipo de actividad con comodines (puntos) para permisos.
 *
 * Exactamente 6 caracteres: dígitos o '.' (p. ej. 1....., 12...., 2.....).
 * No se puede guardar como int: (int)'1.....' = 1 y se pierde el patrón.
 */
final class ActividadTipoIdTxt
{
    private string $value;

    public function __construct(string $value)
    {
        $str = trim($value);
        $this->validate($str);
        $this->value = $str;
    }

    private function validate(string $str): void
    {
        if (!preg_match('/^[\d.]{6}$/', $str)) {
            throw new \InvalidArgumentException(
                'ActividadTipoIdTxt debe tener exactamente 6 caracteres (dígitos o punto)'
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ActividadTipoIdTxt $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
