<?php

namespace src\encargossacd\domain\value_objects;

final class MesNum
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(MesNum $other): bool
    {
        return $this->value === $other->value();
    }
}
