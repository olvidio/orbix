<?php

namespace src\profesores\domain\value_objects;

final class YearNumber
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value < 1 || $value > 3000) {
            throw new \InvalidArgumentException('YearNumber must be between 1 and 3000');
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

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
