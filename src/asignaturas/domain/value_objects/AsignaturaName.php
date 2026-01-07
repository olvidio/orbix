<?php

namespace src\asignaturas\domain\value_objects;

final class AsignaturaName
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
            throw new \InvalidArgumentException('AsignaturaName cannot be empty');
        }
        // UI shows max length 100 (see DatosCampo->setArgument(100))
        if (mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('AsignaturaName must be at most 40 characters');
        }
        // Allow common name characters including accents, spaces, apostrophes, hyphens, underscore, plus, parentheses
        if (!preg_match("/^[\p{L}0-9 .,'’\"\/:_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('AsignaturaName has invalid characters');
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

    public function equals(AsignaturaName $other): bool
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
