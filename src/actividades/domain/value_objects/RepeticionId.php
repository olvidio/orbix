<?php

namespace src\actividades\domain\value_objects;

final class RepeticionId
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
            throw new \InvalidArgumentException('RepeticionId must be a positive integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(RepeticionId $other): bool
    {
        return $this->value === $other->value();
    }
}
