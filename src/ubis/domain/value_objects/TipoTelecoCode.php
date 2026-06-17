<?php

namespace src\ubis\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class TipoTelecoCode
{
    private string $value;

    public function __construct(?string $value)
    {
        $value = $value !== null ? trim($value) : '';
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('TipoCentroName must be at most 20 characters', $value));
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
