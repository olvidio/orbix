<?php

namespace src\dossiers\domain\value_objects;

final class TipoDossierId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('TipoDossierId must be a positive integer');
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(TipoDossierId $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
