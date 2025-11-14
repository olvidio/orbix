<?php

namespace src\asignaturas\domain\value_objects;

final class AsignaturaTipoYear
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
        // Máx 4 caracteres; pensado para dígitos o números romanos (I,V,X)
        if (mb_strlen($value) > 4) {
            throw new \InvalidArgumentException('AsignaturaTipoYear must be at most 4 characters');
        }
        if ($value !== '' && !preg_match('/^[IVX0-9]+$/u', $value)) {
            throw new \InvalidArgumentException('AsignaturaTipoYear must contain only roman numerals (I,V,X) or digits');
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
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
