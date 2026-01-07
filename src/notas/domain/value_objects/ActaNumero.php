<?php

namespace src\notas\domain\value_objects;

/**
 * Value object for Acta Number (sacta)
 */
final class ActaNumero
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
        if ($value === '') {
            throw new \InvalidArgumentException('El número de acta no puede estar vacío');
        }
        if (mb_strlen($value) > 50) {
            throw new \InvalidArgumentException('El número de acta debe tener como máximo 50 caracteres');
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

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
