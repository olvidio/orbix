<?php

namespace src\actividades\domain\value_objects;

final class TemporadaCode
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
        // En UI setArgument(1) así que consideramos un solo carácter de temporada
        if ($value === '') {
            throw new \InvalidArgumentException('TemporadaCode no puede estar vacío');
        }
        if (mb_strlen($value) > 1) {
            throw new \InvalidArgumentException('TemporadaCode debe ser de un solo carácter');
        }
        // Permitir letras y números
        if (!preg_match('/^[\p{L}0-9]$/u', $value)) {
            throw new \InvalidArgumentException('TemporadaCode contiene caracteres no válidos');
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

    public function equals(TemporadaCode $other): bool
    {
        return $this->value === $other->value();
    }
}
