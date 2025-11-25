<?php

namespace src\ubis\domain\value_objects;

final class PlanoNameText
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
            throw new \InvalidArgumentException('PlanoNameText cannot be empty');
        }
        if (mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('PlanoNameText must be at most 100 characters');
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
