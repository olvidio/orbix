<?php

namespace src\profesores\domain\value_objects;

final class ProfesorTipoId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // Permitimos 0 o positivos
        if ($value < 0) {
            throw new \InvalidArgumentException('ProfesorTipoId must be a non-negative integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(ProfesorTipoId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException('ProfesorTipoId string must be digits');
        }
        return new self((int)$value);
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
