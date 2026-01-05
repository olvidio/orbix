<?php

namespace src\asistentes\domain\value_objects;

/**
 * Value object para el encargo de un asistente
 */
final class AsistenteEncargo
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
        if (mb_strlen($value) > 255) {
            throw new \InvalidArgumentException('AsistenteEncargo must be at most 255 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
