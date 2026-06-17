<?php

namespace src\asignaturas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class AsignaturaTipoLatin
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
            throw new \InvalidArgumentException('AsignaturaTipoLatin cannot be empty');
        }
        if (mb_strlen($value) > 25) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('AsignaturaTipoLatin must be at most 25 characters', $value));
        }
        if (!preg_match("/^[\p{L}0-9 .,'’_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('AsignaturaTipoLatin has invalid characters');
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
