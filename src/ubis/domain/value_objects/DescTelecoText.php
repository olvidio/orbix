<?php

namespace src\ubis\domain\value_objects;

final class DescTelecoText
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
            throw new \InvalidArgumentException('DescTelecoText cannot be empty');
        }
        // Por UI, longitud máxima 20 (DatosCampo->setArgument(20))
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('DescTelecoText must be at most 20 characters');
        }
        // Caracteres comunes de nombre/etiqueta, en línea con TipoTelecoName
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('DescTelecoText has invalid characters');
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

    public function equals(int $other): bool
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
