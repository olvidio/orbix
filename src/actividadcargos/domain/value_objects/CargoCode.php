<?php

namespace src\actividadcargos\domain\value_objects;

final class CargoCode
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
            throw new \InvalidArgumentException('CargoCode cannot be empty');
        }
        // Por UI, longitud máxima 2 (DatosCampo->setArgument(2))
        if (mb_strlen($value) > 2) {
            throw new \InvalidArgumentException('CargoCode must be at most 2 characters');
        }
        // Permitimos letras, números, guion y guion bajo
        if (!preg_match('/^[A-Za-z0-9_-]+$/u', $value)) {
            throw new \InvalidArgumentException('CargoCode has invalid characters');
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

    public function equals(CargoCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
