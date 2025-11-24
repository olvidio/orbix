<?php

namespace src\ubis\domain\value_objects;

final class TipoCasaText
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
            throw new \InvalidArgumentException('TipoCasaText cannot be empty');
        }
        if (mb_strlen($value) > 50) {
            throw new \InvalidArgumentException('TipoCasaText must be at most 50 characters');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('TipoCasaText has invalid characters');
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

    public function equals(TipoCasaText $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
