<?php

namespace src\utils_database\domain\value_objects;

final class DbSchemaCode
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
            throw new \InvalidArgumentException('DbSchemaCode cannot be empty');
        }
        // Typical schema codes are short, allow up to 32 chars
        if (mb_strlen($value) > 32) {
            throw new \InvalidArgumentException('DbSchemaCode must be at most 32 characters');
        }
        // Allow letters, numbers, underscore and hyphen
        if (!preg_match('/^[A-Za-z0-9_-]+$/u', $value)) {
            throw new \InvalidArgumentException('DbSchemaCode has invalid characters');
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

    public function equals(DbSchemaCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
