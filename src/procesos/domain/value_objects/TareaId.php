<?php

namespace src\procesos\domain\value_objects;

final class TareaId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('TareaId must be a non-negative integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(TareaId $other): bool
    {
        return $this->value === $other->value();
    }
}
