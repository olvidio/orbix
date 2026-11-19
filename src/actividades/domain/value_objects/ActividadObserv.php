<?php

namespace src\actividades\domain\value_objects;

final class ActividadObserv
{
    private string $value;

    public function __construct(string $value)
    {
        // Texto pegado de una hoja de cálculo: cada grupo de tabuladores o saltos de línea pasa a ser un espacio.
        $value = trim(preg_replace('/[\t\r\n]+/', ' ', $value));
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!preg_match("/^[\p{L}\p{M}\p{N}\p{P}\p{S}\p{Z}]*$/u", $value)) {
            throw new \InvalidArgumentException('ActividadObserv has invalid characters');
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

    public function equals(ActividadObserv $other): bool
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
