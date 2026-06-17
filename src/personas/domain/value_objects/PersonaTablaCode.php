<?php

namespace src\personas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class PersonaTablaCode
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
            throw new \InvalidArgumentException('PersonaTablaCode cannot be empty');
        }
        if (mb_strlen($value) > 6) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('PersonaTablaCode must be at most 6 characters', $value));
        }
        PersonaTextoChars::throwsIfNotMatching('PersonaTablaCode', $value, PersonaTextoChars::CLASE_TABLA_CODE);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
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
