<?php

namespace src\configuracion\domain\value_objects;

final class ModuloDescription
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
        // Description can be empty? We typically allow non-empty to encourage signal.
        // If you prefer to allow empty, change validation accordingly.
        if ($value === '') {
            throw new \InvalidArgumentException('ModuloDescription cannot be empty');
        }
        // Conservative maximum length to prevent excessively large strings (tune as needed)
        if (mb_strlen($value) > 500) {
            throw new \InvalidArgumentException('ModuloDescription must be at most 500 characters');
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

    public function equals(ModuloDescription $other): bool
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
