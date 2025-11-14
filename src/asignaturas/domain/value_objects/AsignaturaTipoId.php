<?php

namespace src\asignaturas\domain\value_objects;

final class AsignaturaTipoId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // Debe ser un entero positivo de 1 dígitos (tabla xa_tipo_asig)
        $esRangoNormal = ($value >= 1 && $value <= 9);
        if (!$esRangoNormal) {
            throw new \InvalidArgumentException('AsignaturaTipoId must be a non-negative integer');
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

    public function equals(AsignaturaTipoId $other): bool
    {
        return $this->value === $other->value();
    }
}
