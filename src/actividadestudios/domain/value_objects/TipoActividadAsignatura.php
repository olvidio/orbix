<?php

namespace src\actividadestudios\domain\value_objects;

final class TipoActividadAsignatura
{

    const TIPO_CA = 'v'; // Verano: ca,cv.
    const TIPO_INV = 'i'; // Semestre de invierno.
    const TIPO_PRECEPTOR = 'p'; // Preceptor.

    public static function getTiposActividad(): array
    {
        return [
            self::TIPO_CA => _("ca/cv"),
            self::TIPO_INV => _("sem. invierno"),
            self::TIPO_PRECEPTOR => _("preceptor")
        ];
    }

    // ---------------------------------------------------------------------------
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        $allowed = [
            self::TIPO_CA,
            self::TIPO_INV,
            self::TIPO_PRECEPTOR,
        ];

        if (!in_array($value, $allowed, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value for TipoActividadAsignatura: %s. Allowed values: %s',
                $value,
                implode(', ', $allowed)
            ));
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

    public static function fromNullable(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }
        return new self($value);
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
