<?php

namespace src\dossiers\domain\value_objects;

final class TipoDossierDescripcion
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('TipoDossierDescripcion cannot be empty');
        }
        if (mb_strlen($value) > 70) {
            throw new \InvalidArgumentException('TipoDossierDescripcion must be at most 70 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
