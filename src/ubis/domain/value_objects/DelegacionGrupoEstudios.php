<?php

namespace src\ubis\domain\value_objects;

final class DelegacionGrupoEstudios
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = strtoupper($value);
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('DelegacionGrupoEstudios cannot be empty');
        }
        if (mb_strlen($value) > 3) {
            throw new \InvalidArgumentException('DelegacionGrupoEstudios must be at most 3 characters');
        }
        if (!preg_match('/^[A-Za-z0-9]+$/u', $value)) {
            throw new \InvalidArgumentException('DelegacionGrupoEstudios has invalid characters');
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

    public function equals(DelegacionGrupoEstudios $other): bool
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
