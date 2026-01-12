<?php

namespace src\ubis\domain\value_objects;

final class DireccionId
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
            throw new \InvalidArgumentException('DireccionId must be a positive integer');
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

    public static function fromNullableInt(?int $value): self
    {
        if ($value === null) { return new self(0); }
        return new self($value);
    }
}
