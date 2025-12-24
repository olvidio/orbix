<?php

namespace src\personas\domain\value_objects;

final class CeNumber
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // PostgreSQL smallint range
        if ($value < -32768 || $value > 32767) {
            throw new \InvalidArgumentException('CeNumber must be within smallint range');
        }
    }

    public function value(): int { return $this->value; }
    public function __toString(): string { return (string)$this->value; }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
