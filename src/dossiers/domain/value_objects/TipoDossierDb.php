<?php

namespace src\dossiers\domain\value_objects;

final class TipoDossierDb
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value < 1 || $value > 3) {
            throw new \InvalidArgumentException('TipoDossierDb must be between 1 and 3');
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

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
