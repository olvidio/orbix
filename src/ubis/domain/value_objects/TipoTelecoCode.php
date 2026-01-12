<?php

namespace src\ubis\domain\value_objects;

final class TipoTelecoCode
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
            throw new \InvalidArgumentException('TipoTelecoCode cannot be empty');
        }
        // Por UI, longitud máxima 10 (DatosCampo->setArgument(10))
        if (mb_strlen($value) > 10) {
            throw new \InvalidArgumentException('TipoTelecoCode must be at most 10 characters');
        }
        // Permitimos letras, números, guion y guion bajo
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('TipoTelecoCode has invalid characters');
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

    public function equals(TipoTelecoCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
