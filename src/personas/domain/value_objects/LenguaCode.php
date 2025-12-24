<?php

namespace src\personas\domain\value_objects;

final class LenguaCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = strtoupper($value);
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('LenguaCode cannot be empty');
        }
        if (mb_strlen($value) > 3) {
            throw new \InvalidArgumentException('LenguaCode must be at most 3 characters');
        }
        if (!preg_match('/^[A-Za-z]{1,3}$/', $value)) {
            throw new \InvalidArgumentException('LenguaCode must be alphabetic up to 3 chars');
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
