<?php

namespace src\ubis\domain\value_objects;

final class TipoTelecoId
{
    private int $value;

    public function __construct(?string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('TipoLaborId must be a non-negative integer');
        }
    }

    public function value(): string
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
