<?php

namespace src\tablonanuncios\domain\value_objects;

final class UsuarioCreador
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
            throw new \InvalidArgumentException('UsuarioCreador cannot be empty');
        }
        if (mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('UsuarioCreador must be at most 100 characters');
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

    public function equals(UsuarioCreador $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        return new self($value);
    }
}
