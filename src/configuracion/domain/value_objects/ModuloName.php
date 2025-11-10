<?php

namespace src\configuracion\domain\value_objects;

final class ModuloName
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
            throw new \InvalidArgumentException('ModuloName cannot be empty');
        }
        // Conservative max length (UI dependent). Adjust if needed.
        if (mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('ModuloName must be at most 100 characters');
        }
        // Allow letters (incl. accents), numbers, spaces and common punctuation
        if (!preg_match("/^[\p{L}0-9 .,'’\-()]+$/u", $value)) {
            throw new \InvalidArgumentException('ModuloName has invalid characters');
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

    public function equals(ModuloName $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
