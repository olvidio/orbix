<?php

namespace src\ubis\domain\value_objects;

final class RegionCode
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
            throw new \InvalidArgumentException('RegionCode cannot be empty');
        }
        // By UI config, argument length is 6
        if (mb_strlen($value) > 6) {
            throw new \InvalidArgumentException('RegionCode must be at most 6 characters');
        }
        // Allow letters, numbers, underscore and hyphen
        if (!preg_match('/^[A-Za-z0-9_-]+$/u', $value)) {
            throw new \InvalidArgumentException('RegionCode has invalid characters');
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

    public function equals(RegionCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
