<?php

namespace src\actividadessacd\domain\value_objects;

final class SacdTextoTexto
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(SacdTextoTexto $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        return $value !== null ? new self($value) : null;
    }
}
