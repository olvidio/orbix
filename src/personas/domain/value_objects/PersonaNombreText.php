<?php

namespace src\personas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

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
        if (mb_strlen($value) > 50) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext(
                'PersonaNombreText must be at most 50 characters',
                $value
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
