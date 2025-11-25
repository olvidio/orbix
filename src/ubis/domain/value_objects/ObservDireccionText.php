<?php

namespace src\ubis\domain\value_objects;

final class ObservDireccionText
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
            throw new \InvalidArgumentException('ObservDireccionText cannot be empty');
        }
        if (mb_strlen($value) > 255) {
            throw new \InvalidArgumentException('ObservDireccionText must be at most 255 characters');
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
