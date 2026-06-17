<?php

namespace src\asignaturas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class AsignaturaShortName
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
            throw new \InvalidArgumentException('AsignaturaShortName cannot be empty');
        }
        // UI shows max length 23 (see DatosCampo->setArgument(23))
        if (mb_strlen($value) > 23) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('AsignaturaShortName must be at most 30 characters', $value));
        }
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('AsignaturaShortName has invalid characters');
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

    public function equals(AsignaturaShortName $other): bool
    {
        return $this->value === $other->value();
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
