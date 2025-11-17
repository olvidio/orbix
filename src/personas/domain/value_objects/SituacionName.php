<?php

namespace src\personas\domain\value_objects;

final class SituacionName
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
            throw new \InvalidArgumentException('SituacionName cannot be empty');
        }
        // Por UI, longitud máxima 60 (DatosCampo->setArgument(60))
        if (mb_strlen($value) > 60) {
            throw new \InvalidArgumentException('SituacionName must be at most 60 characters');
        }
        // Caracteres comunes de nombre, incluyendo acentos, espacios, guiones, paréntesis, subrayado y +
        if (!preg_match("/^[\p{L}0-9 .,'’_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('SituacionName has invalid characters');
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

    public function equals(SituacionName $other): bool
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
