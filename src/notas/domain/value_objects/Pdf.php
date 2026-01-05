<?php

namespace src\notas\domain\value_objects;

/**
 * Value object for Pdf
 */
final class Pdf
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        return new self($value);
    }
}
