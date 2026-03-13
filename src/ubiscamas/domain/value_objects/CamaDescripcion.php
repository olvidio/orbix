<?php

namespace src\ubiscamas\domain\value_objects;

final class CamaDescripcion
{
    private string $value;

    public function __construct(string $value)
    {
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

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || $value === '') { return null; }
        return new self($value);
    }
}
