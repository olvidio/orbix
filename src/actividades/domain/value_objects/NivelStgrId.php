<?php

namespace src\actividades\domain\value_objects;

final class NivelStgrId
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
            throw new \InvalidArgumentException('NivelStgrId must be a positive integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(NivelStgrId $other): bool
    {
        return $this->value === $other->value();
    }
}
