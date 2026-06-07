<?php

namespace src\ubis\domain\value_objects;

final class TipoTelecoId
{
    private int $value;

    public function __construct(?int $value)
    {
        if ($value === null) {
            $value = 0;
        }
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('TipoTelecoId must be a non-negative integer');
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

    public function equals(TipoTelecoId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}