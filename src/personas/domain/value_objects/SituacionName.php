<?php

namespace src\personas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class SituacionName
{
    private string $value;

    public function __construct(string $value)
    {
        $value = PersonaTextoChars::normalizeTipografico(trim($value));
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('SituacionName cannot be empty');
        }
        if (mb_strlen($value) > 60) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('SituacionName must be at most 60 characters', $value));
        }
        PersonaTextoChars::throwsIfNotMatching('SituacionName', $value, PersonaTextoChars::CLASE_SITUACION);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(SituacionName $other): bool
    {
        return $this->value === $other->value();
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
