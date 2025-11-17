<?php

namespace src\ubis\domain\value_objects;

final class TipoTelecoName
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
            throw new \InvalidArgumentException('TipoTelecoName cannot be empty');
        }
        // Por UI, longitud máxima 20 (DatosCampo->setArgument(20))
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('TipoTelecoName must be at most 20 characters');
        }
        // Caracteres comunes de nombre, incluyendo acentos, espacios, guiones, subrayado, paréntesis y +
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('TipoTelecoName has invalid characters');
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

    public function equals(TipoTelecoName $other): bool
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
