<?php
namespace src\notas\domain\value_objects;

final class Libro
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
