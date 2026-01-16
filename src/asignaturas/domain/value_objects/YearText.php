<?php

namespace src\asignaturas\domain\value_objects;

final class YearText
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
        // UI max length 4
        if (mb_strlen($value) !== 1) {
            throw new \InvalidArgumentException('Year must be exactly 1 characters');
        }
        // Allow empty handled by factory; here ensure it is digits if not empty
        if ($value !== '' && !ctype_digit($value)) {
            throw new \InvalidArgumentException('Year must be digits');
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
