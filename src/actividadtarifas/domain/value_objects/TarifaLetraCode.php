<?php

namespace src\actividadtarifas\domain\value_objects;

final class TarifaLetraCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('TarifaLetraCode no puede estar vacío');
        }
        if (mb_strlen($value) > 6) {
            throw new \InvalidArgumentException('TarifaLetraCode debe tener un máximo de 6 carácteres');
        }
        // Quizá tambien puede tener espacios y coma
        if (!preg_match('/^[A-Z ,]*$/', $value)) {
            throw new \InvalidArgumentException('TarifaLetraCode deben ser letras: A-Z (máx. 6)');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(TarifaLetraCode $other): bool
    {
        return $this->value === $other->value();
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
