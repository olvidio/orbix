<?php

namespace src\actividades\domain\value_objects;

final class ActividadTipoId
{
    private int $value;

    /**
     * @param int|string $value Debe representar exactamente 6 dígitos.
     */
    public function __construct(?int $value)
    {
        // Aceptar string para poder validar longitud exacta; luego guardar como int
        $str = (string)$value;
        $str = trim($str);
        $this->validate($str);
        $this->value = (int)$str;
    }

    private function validate(string $str): void
    {
        if ($str === null) {
            return;
        }
        if (!preg_match('/^\d{6}$/', $str)) {
            throw new \InvalidArgumentException('ActividadTipoId debe tener exactamente 6 dígitos');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(ActividadTipoId $other): bool
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
