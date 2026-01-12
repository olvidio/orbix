<?php

namespace src\actividadcargos\domain\value_objects;

/**
 * Value object para las observaciones de un cargo en una actividad
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/01/2026
 */
final class ObservacionesCargo
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Devuelve el valor
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compara con otro objeto
     *
     * @param ObservacionesCargo $other
     * @return bool
     */
    public function equals(ObservacionesCargo $other): bool
    {
        return $this->value === $other->value();
    }

    /**
     * Representación en string
     *
     * @return string
     */
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
