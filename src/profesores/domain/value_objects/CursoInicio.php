<?php

namespace src\profesores\domain\value_objects;

final class CursoInicio
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // año académico inicio, permitir 1900-2100
        if ($value < 1900 || $value > 2100) {
            throw new \InvalidArgumentException('CursoInicio must be between 1900 and 2100');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) { return null; }
        return new self($value);
    }
}
