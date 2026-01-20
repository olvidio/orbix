<?php

namespace src\ubis\domain\value_objects;

final class TipoTelecoCode
{
    private string $value;

    public function __construct(?string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('TipoCentroName must be at most 20 characters');
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

    public function equals(TipoTelecoCode $other): bool
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
