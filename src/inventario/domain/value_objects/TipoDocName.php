<?php

namespace src\inventario\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class TipoDocName
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
            throw new \InvalidArgumentException('TipoDocName cannot be empty');
        }
        if (mb_strlen($value) > 60) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('TipoDocName must be at most 60 characters', $value));
        }
        if (!preg_match("/^[\p{L}0-9 .,'’\-()\/]+$/u", $value)) {
            throw new \InvalidArgumentException('TipoDocName has invalid characters');
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

    public function equals(TipoDocName $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
