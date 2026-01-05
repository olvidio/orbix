<?php

namespace src\cartaspresentacion\domain\value_objects;

final class PresEmailText
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
            throw new \InvalidArgumentException('PresEmailText cannot be empty');
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            // Nota: Podría ser un error en la base de datos existente, pero para un VO de dominio queremos validación.
            // Si el legacy permite cualquier cosa, quizás mejor no validar estrictamente o atraparlo.
            // Para ser consistentes con Email VO de usuarios:
            // throw new \InvalidArgumentException('Invalid email format: ' . $value);
            // Pero en este caso, lo dejaré solo como texto por si acaso hay múltiples emails o formato libre.
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
        if ($value === null || trim($value) === '') {
            return null;
        }
        return new self($value);
    }
}
