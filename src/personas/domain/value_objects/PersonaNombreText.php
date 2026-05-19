<?php

namespace src\personas\domain\value_objects;

final class PersonaNombreText
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
            throw new \InvalidArgumentException('PersonaNombreText cannot be empty');
        }
        $len = mb_strlen($value);
        if ($len > 50) {
            throw new \InvalidArgumentException(sprintf(
                'PersonaNombreText must be at most 50 characters (length=%d, value=%s)',
                $len,
                PersonaTextoChars::safeRepr($value)
            ));
        }
        PersonaTextoChars::throwsIfNotMatching('PersonaNombreText', $value, PersonaTextoChars::CLASE_TEXTO_PERSONA);
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
