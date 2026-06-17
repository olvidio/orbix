<?php

namespace src\dossiers\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

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
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('TipoDossierDescripcion must be at most 70 characters', $value));
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
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
