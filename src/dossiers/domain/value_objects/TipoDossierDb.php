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
        $posibles = [1, 2, 3, 5];
        if (!in_array($value, $posibles)) {
            throw new \InvalidArgumentException('TipoDossierDb must be 1,2,3 or 5');
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
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
