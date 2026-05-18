<?php

namespace src\personas\domain\value_objects;

final class ApelFamText
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
            throw new \InvalidArgumentException('ApelFamText cannot be empty');
        }
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('ApelFamText must be at most 20 characters');
        }
        PersonaTextoChars::throwsIfNotMatching('ApelFamText', $value, PersonaTextoChars::CLASE_TEXTO_PERSONA);
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
