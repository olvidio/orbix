<?php

namespace src\personas\domain\value_objects;

final class LenguaCode
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
            throw new \InvalidArgumentException('LenguaCode cannot be empty');
        }
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}\.[A-Z0-9\-]+$/', $value)) {
            throw new \InvalidArgumentException('LenguaCode must follow the format: xx_XX.ENCODING (e.g., es_ES.UTF-8)');
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
