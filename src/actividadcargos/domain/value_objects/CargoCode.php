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
        // Por UI, longitud máxima 8 (DatosCampo->setArgument(8))
        if (mb_strlen($value) > 8) {
            throw new \InvalidArgumentException('CargoCode must be at most 8 characters');
        }
        // Allow common name characters including accents, spaces, apostrophes, hyphens, underscore, plus, parentheses
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
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
