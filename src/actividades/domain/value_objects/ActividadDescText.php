<?php

namespace src\actividades\domain\value_objects;

final class ActividadDescText
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
            throw new \InvalidArgumentException('ActividadDescText must be a non-empty string');
        }
        if (mb_strlen($value) > 80) {
            throw new \InvalidArgumentException('NivelStgrDesc debe tener como máximo 80 caracteres');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('NivelStgrDesc contiene caracteres no válidos');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ActividadDescText $other): bool
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
