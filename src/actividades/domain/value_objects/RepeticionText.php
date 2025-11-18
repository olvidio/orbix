<?php

namespace src\actividades\domain\value_objects;

final class RepeticionText
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
            throw new \InvalidArgumentException('RepeticionText no puede estar vacío');
        }
        // UI: argument(50)
        if (mb_strlen($value) > 50) {
            throw new \InvalidArgumentException('RepeticionText debe tener como máximo 50 caracteres');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('RepeticionText contiene caracteres no válidos');
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

    public function equals(RepeticionText $other): bool
    {
        return $this->value === $other->value();
    }
}
