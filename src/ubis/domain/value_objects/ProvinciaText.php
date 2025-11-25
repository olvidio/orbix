<?php

namespace src\ubis\domain\value_objects;

final class ProvinciaText
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
            throw new \InvalidArgumentException('ProvinciaText cannot be empty');
        }
        if (mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('ProvinciaText must be at most 100 characters');
        }
        if (!preg_match("/^[\p{L} .,'´’:_\-()]+$/u", $value)) {
            throw new \InvalidArgumentException('ProvinciaText has invalid characters');
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
