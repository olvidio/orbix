<?php

namespace src\ubis\domain\value_objects;

final class DireccionId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        $valueStr = (string)$value;
        $isValid = preg_match('/^[12]\d{4,}$/', $valueStr) || preg_match('/^\-[12]\d{4,}$/', $valueStr);

        if (!$isValid) {
            throw new \InvalidArgumentException("El valor debe empezar por 1, 2, -1 o -2 y tener al menos 5 dígitos");
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

    public static function fromNullableInt(?int $value): self
    {
        if ($value === null) { return new self(0); }
        return new self($value);
    }
}
