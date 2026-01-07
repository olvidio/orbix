<?php

namespace src\encargossacd\domain\value_objects;

final class EncargoTipoId
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

    public function equals(EncargoTipoId $other): bool
    {
        return $this->value === $other->value();
    }

public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
