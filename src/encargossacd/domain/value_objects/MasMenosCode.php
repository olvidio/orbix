<?php

namespace src\encargossacd\domain\value_objects;

final class MasMenosCode
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

    public function equals(MasMenosCode $other): bool
    {
        return $this->value === $other->value();
    }
}
