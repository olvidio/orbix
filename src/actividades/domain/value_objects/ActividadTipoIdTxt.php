<?php

namespace src\actividades\domain\value_objects;

final class ActividadTipoIdTxt
{
    private string $value;

    /**
     * @param int|string $value Debe representar exactamente 6 dígitos.
     */
    public function __construct(string $value)
    {
        // Aceptar string para poder validar longitud exacta; luego guardar como int
        $str = (string)$value;
        $str = trim($str);
        $this->validate($str);
        $this->value = $str;
    }

    private function validate(string $str): void
    {
        if (!preg_match('/^[\d\.]{6}$/', $str)) {
            throw new \InvalidArgumentException('ActividadTipoId debe tener exactamente 6 dígitos');
        }
    }

    public function value(): int
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
