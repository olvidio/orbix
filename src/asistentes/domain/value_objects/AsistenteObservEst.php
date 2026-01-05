<?php

namespace src\asistentes\domain\value_objects;

/**
 * Value object para las observaciones de estudios de un asistente
 */
final class AsistenteObservEst
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
