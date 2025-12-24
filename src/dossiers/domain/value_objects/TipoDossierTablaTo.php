<?php

namespace src\dossiers\domain\value_objects;

final class TipoDossierTablaTo
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
            throw new \InvalidArgumentException('TipoDossierTablaTo cannot be empty');
        }
        if (mb_strlen($value) > 25) {
            throw new \InvalidArgumentException('TipoDossierTablaTo must be at most 25 characters');
        }
        if (!preg_match("/^[A-Za-z0-9_]+$/", $value)) {
            throw new \InvalidArgumentException('TipoDossierTablaTo has invalid characters');
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
