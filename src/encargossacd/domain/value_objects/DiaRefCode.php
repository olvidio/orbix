<?php

namespace src\encargossacd\domain\value_objects;

final class DiaRefCode
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

    public function equals(DiaRefCode $other): bool
    {
        return $this->value === $other->value();
    }
}
