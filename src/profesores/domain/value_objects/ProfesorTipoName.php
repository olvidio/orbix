<?php

namespace src\profesores\domain\value_objects;

final class ProfesorTipoName
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
            throw new \InvalidArgumentException('ProfesorTipoName cannot be empty');
        }
        // UI muestra longitud máxima 50 (DatosCampo->setArgument(50))
        if (mb_strlen($value) > 50) {
            throw new \InvalidArgumentException('ProfesorTipoName must be at most 50 characters');
        }
        // Caracteres comunes de nombre (incluye acentos, espacios, guiones, subrayado, paréntesis y +)
        if (!preg_match("/^[\p{L}0-9 .,'’_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('ProfesorTipoName has invalid characters');
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

    public function equals(ProfesorTipoName $other): bool
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
