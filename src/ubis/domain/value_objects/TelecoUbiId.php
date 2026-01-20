<?php

namespace src\ubis\domain\value_objects;

final class TelecoUbiId
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
        $isValid = preg_match('/^[123]\d{4,}$/', $valueStr) || preg_match('/^-[123]\d{4,}$/', $valueStr);

        if (!$isValid) {
            throw new \InvalidArgumentException("El valor debe empezar por 1, 2, 3 o -1, -2, -3 y tener al menos 5 dígitos");
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

    public function equals(TelecoUbiId $other): bool
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
