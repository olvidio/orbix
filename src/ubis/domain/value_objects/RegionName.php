<?php

namespace src\ubis\domain\value_objects;

final class RegionName
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
            throw new \InvalidArgumentException('RegionName cannot be empty');
        }
        // UI shows max length 30
        if (mb_strlen($value) > 30) {
            throw new \InvalidArgumentException('RegionName must be at most 30 characters');
        }
        // Allow common name characters including accents, spaces, apostrophes, hyphens
        if (!preg_match("/^[\p{L}0-9 .,'’\-()]+$/u", $value)) {
            throw new \InvalidArgumentException('RegionName has invalid characters');
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

    public function equals(RegionName $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
