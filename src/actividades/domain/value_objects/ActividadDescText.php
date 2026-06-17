<?php

namespace src\actividades\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

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
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('ActividadDescText debe tener como máximo 80 caracteres', $value));
        }
        if (!preg_match("/^[\p{L}\p{M}\p{N}\p{P}\p{S}\p{Z}]*$/u", $value)) {
            throw new \InvalidArgumentException('ActividadDescText contiene caracteres no válidos');
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
         if ($value === null) {
             return null;
         }
         $value_trimmed = trim($value);
         if ($value_trimmed === '') {
             return null;
         }
         return new self($value_trimmed);
     }
}
