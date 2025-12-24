<?php

namespace src\personas\domain\value_objects;

final class LugarNacimientoText
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
            throw new \InvalidArgumentException('LugarNacimientoText cannot be empty');
        }
        if (mb_strlen($value) > 255) {
            throw new \InvalidArgumentException('LugarNacimientoText must be at most 255 characters');
        }
    }

    public function value(): string { return $this->value; }
    public function __toString(): string { return $this->value; }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
