<?php

namespace src\dossiers\domain\value_objects;

final class TipoDossierCampoTo
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
            throw new \InvalidArgumentException('TipoDossierCampoTo cannot be empty');
        }
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('TipoDossierCampoTo must be at most 20 characters');
        }
        if (!preg_match("/^[A-Za-z0-9_]+$/", $value)) {
            throw new \InvalidArgumentException('TipoDossierCampoTo has invalid characters');
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

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
