<?php

namespace src\ubis\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class PlanoDocText
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
            throw new \InvalidArgumentException('PlanoDocText cannot be empty');
        }
        if (mb_strlen($value) > 255) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('PlanoDocText must be at most 255 characters', $value));
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
