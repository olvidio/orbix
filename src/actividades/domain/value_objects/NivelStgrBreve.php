<?php

namespace src\actividades\domain\value_objects;

final class NivelStgrBreve
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
            throw new \InvalidArgumentException('NivelStgrBreve no puede estar vacío');
        }
        // UI: argument(2)
        if (mb_strlen($value) > 2) {
            throw new \InvalidArgumentException('NivelStgrBreve debe tener como máximo 2 caracteres');
        }
        if (!preg_match('/^[\p{L}0-9]?[\p{L}0-9]?$/u', $value)) {
            throw new \InvalidArgumentException('NivelStgrBreve contiene caracteres no válidos');
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

    public function equals(NivelStgrBreve $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }
        return new self($value);
    }
}
