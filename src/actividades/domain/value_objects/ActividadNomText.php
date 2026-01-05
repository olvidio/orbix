<?php

namespace src\actividades\domain\value_objects;

final class ActividadNomText
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('ActividadNomText must be a non-empty string');
        }
        if (mb_strlen($value) > 255) {
            throw new \InvalidArgumentException('ActividadNomText length must be <= 255');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’`´:_\-()\+\/\*]+$/u", $value)) {
            throw new \InvalidArgumentException('ActividadNomText contiene caracteres no válidos');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ActividadNomText $other): bool
    {
        return $this->value === $other->value();
    }

     public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
