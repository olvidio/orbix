<?php

namespace src\ubis\domain\value_objects;

final class TelecoUbiItemId
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
            throw new \InvalidArgumentException('TelecoUbiItemId must be a positive integer');
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

    public function equals(TelecoUbiItemId $other): bool
    {
        return $this->value === $other->value();
    }
}
