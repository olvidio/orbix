<?php

namespace src\actividades\domain\value_objects;

final class ActividadTipoId
{
    private int $value;

    public function __construct(?int $value)
    {
        if ($value === null) {
            throw new \InvalidArgumentException('ActividadTipoId no puede ser null');
        }
        $str = trim((string) $value);
        $this->validate($str);
        $this->value = (int) $str;
    }

    private function validate(string $str): void
    {
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
