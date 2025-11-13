<?php

namespace src\utils_database\domain\value_objects;

final class MapObjectCode
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
            throw new \InvalidArgumentException('MapObjectCode cannot be empty');
        }
        // keep it short and strict as it maps to known object names
        if (mb_strlen($value) > 32) {
            throw new \InvalidArgumentException('MapObjectCode must be at most 32 characters');
        }
        // Allow letters, numbers, underscore and hyphen (object names like Actividad, Casa, Direccion, Centro)
        if (!preg_match('/^[A-Za-z0-9_-]+$/u', $value)) {
            throw new \InvalidArgumentException('MapObjectCode has invalid characters');
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

    public function equals(MapObjectCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
