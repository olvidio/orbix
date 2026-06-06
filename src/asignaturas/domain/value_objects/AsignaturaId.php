<?php

namespace src\asignaturas\domain\value_objects;

final class AsignaturaId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // Debe ser un entero positivo de 4 dígitos que empiece por 1, 2 o 3
        // excepto que se permite explícitamente 9998 y 9999.
        $esRangoNormal = ($value >= 1000 && $value <= 3999);
        $esExcepcion = ($value === 9998 || $value === 9999);

        if (!($esRangoNormal || $esExcepcion)) {
            throw new \InvalidArgumentException(
                'AsignaturaId must be a 4-digit integer starting with 1, 2 or 3, or be 9998/9999'
            );
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

    public function equals(AsignaturaId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        // Solo dígitos
        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException('AsignaturaId string must be digits');
        }

        // Debe tener exactamente 4 dígitos
        if (strlen($value) !== 4) {
            throw new \InvalidArgumentException('AsignaturaId string must be exactly 4 digits');
        }

        return new self((int)$value);
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
