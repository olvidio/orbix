<?php

namespace src\asistentes\domain\value_objects;

/**
 * Value object para las observaciones de un asistente
 */
final class AsistenteObserv
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
        // Sin restricciones específicas de longitud por ahora, pero validamos que no sea excesivo si fuera necesario
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
