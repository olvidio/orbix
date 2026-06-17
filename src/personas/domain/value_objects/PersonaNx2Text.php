<?php

namespace src\personas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class PersonaNx2Text
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
            throw new \InvalidArgumentException('PersonaNx2Text cannot be empty');
        }
        if (mb_strlen($value) > 7) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('PersonaNx2Text must be at most 7 characters', $value));
        }
        PersonaTextoChars::throwsIfNotMatching('PersonaNx2Text', $value, PersonaTextoChars::CLASE_NX);
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
