<?php

namespace src\actividades\domain\value_objects;

/**
 * Value Object para el nombre del tipo de actividad
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 */
final class TipoActivNombre
{
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param TipoActivNombre $other
     * @return bool
     */
    public function equals(TipoActivNombre $other): bool
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
