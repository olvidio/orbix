<?php

namespace src\ubis\domain\value_objects;

final class RegionId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('RegionId must be a positive integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(RegionId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException('RegionId string must be digits');
        }
        return new self((int)$value);
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);

    }
}
