<?php

namespace src\actividadplazas\domain\value_objects;

final class PeticionTipo
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
            throw new \InvalidArgumentException('PeticionTipo no puede estar vacío');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        return new self($value);
    }
}
