<?php

namespace src\actividadestudios\domain\value_objects;

use web\DateTimeLocal;
use web\NullDateTimeLocal;

final class FechaFin
{
    private DateTimeLocal $value;

    public function __construct(DateTimeLocal $value)
    {
        $this->value = $value;
    }

    public function value(): DateTimeLocal
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullable(DateTimeLocal|NullDateTimeLocal|null $value): ?self
    {
        if ($value === null || $value instanceof NullDateTimeLocal) { return null; }
        return new self($value);
    }
}
