<?php
namespace src\notas\domain\value_objects;

final class Descripcion
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
        return new self($value);
    }
}
