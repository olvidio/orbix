<?php

namespace src\dossiers\domain\value_objects;

final class TipoDossierTablaFrom
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
            throw new \InvalidArgumentException('TipoDossierTablaFrom cannot be empty');
        }
        if (mb_strlen($value) > 1) {
            throw new \InvalidArgumentException('TipoDossierTablaFrom must be a 1-character code');
        }
        if (!preg_match('/^[A-Za-z]$/', $value)) {
            throw new \InvalidArgumentException('TipoDossierTablaFrom must be a single letter');
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
