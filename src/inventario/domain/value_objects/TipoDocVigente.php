<?php

namespace src\inventario\domain\value_objects;

final class TipoDocVigente
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    public function equals(TipoDocVigente $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromScalar(mixed $value): self
    {
        return new self((bool)$value);
    }
}
