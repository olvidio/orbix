<?php

namespace src\personas\domain\value_objects;

final class ApelFamText
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
            throw new \InvalidArgumentException('ApelFamText cannot be empty');
        }
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('ApelFamText must be at most 20 characters');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’´\-()]+$/u", $value)) {
            throw new \InvalidArgumentException('ApelFamText has invalid characters');
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
