<?php

namespace src\personas\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;

final class ObservText
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
            throw new \InvalidArgumentException('ObservText cannot be empty');
        }
        if (mb_strlen($value) > 5000) {
            throw new \InvalidArgumentException(ValueObjectMessages::withValueContext('ObservText must be at most 5000 characters', $value));
        }
    }

    public function value(): string { return $this->value; }
    public function __toString(): string { return $this->value; }

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
