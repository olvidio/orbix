<?php

namespace src\ubis\domain\value_objects;

final class UbiNombreText
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
            throw new \InvalidArgumentException('UbiNombreText cannot be empty');
        }
        // Longitud razonable para nombres
        if (mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('UbiNombreText must be at most 100 characters');
        }
        // Caracteres comunes de nombres/títulos
        if (!preg_match("/^[\p{L}0-9 .,'´’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('UbiNombreText has invalid characters');
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

    public function equals(UbiNombreText $other): bool
    {
        return $this->value === $other->value();
    }
}
