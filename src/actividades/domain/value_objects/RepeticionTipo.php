<?php

namespace src\actividades\domain\value_objects;

final class RepeticionTipo
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // En UI se limita a 1 carácter;
        // 0 no se repite; 1 por semana; 2 fijo; 3 sem sta.
        if ($value < 0 || $value > 3) {
            throw new \InvalidArgumentException('RepeticionTipo debe ser un dígito entre 0 y 3');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(RepeticionTipo $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
