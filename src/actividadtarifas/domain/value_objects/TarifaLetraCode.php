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
        if (!preg_match('/^[A-Z]*$/', $value)) {
            throw new \InvalidArgumentException('TarifaLetraCode debe ser una letra A-Z');
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
