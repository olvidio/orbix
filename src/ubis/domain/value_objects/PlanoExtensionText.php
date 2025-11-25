<?php

namespace src\ubis\domain\value_objects;

final class PlanoExtensionText
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('PlanoExtensionText cannot be empty');
        }
        if (mb_strlen($value) > 10) {
            throw new \InvalidArgumentException('PlanoExtensionText must be at most 10 characters');
        }
        if (!preg_match('/^[a-z0-9]+$/', $value)) {
            throw new \InvalidArgumentException('PlanoExtensionText has invalid characters');
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
