<?php

namespace src\procesos\domain\value_objects;

final class TareaObserv
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        // Texto libre (como las observaciones de una actividad): letras, números, puntuación, símbolos
        // y espacios. La lista corta anterior rechazaba «¿falta?», «&», «;», «!»…
        if (!preg_match("/^[\p{L}\p{M}\p{N}\p{P}\p{S}\p{Z}]*$/u", $value)) {
            throw new \InvalidArgumentException('TareaObserv has invalid characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(TareaObserv $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
