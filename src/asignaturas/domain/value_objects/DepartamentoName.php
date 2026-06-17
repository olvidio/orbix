<?php

namespace src\asignaturas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class DepartamentoName
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
            throw new \InvalidArgumentException('DepartamentoName cannot be empty');
        }
        // UI shows max length 50 (see DatosCampo->setArgument(50))
        if (mb_strlen($value) > 50) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('DepartamentoName must be at most 50 characters', $value));
        }
        // Allow common name characters including accents, spaces, apostrophes, hyphens, underscore, plus, parentheses
        if (!preg_match("/^[\p{L}0-9 .,'’_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('DepartamentoName has invalid characters');
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

    public function equals(DepartamentoName $other): bool
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
