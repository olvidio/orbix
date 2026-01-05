<?php

namespace src\zonassacd\domain\value_objects;

/**
 * Value object for nombre_zona
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 2026-01-01
 */
final class NombreZona
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $nombre_zona
     * @throws \InvalidArgumentException If the nombre_zona is invalid
     */
    public function __construct(string $nombre_zona)
    {
        $this->validate($nombre_zona);
        $this->value = $nombre_zona;
    }

    /**
     * Validate the nombre_zona
     *
     * @param string $nombre_zona
     * @throws \InvalidArgumentException If the nombre_zona is invalid
     */
    private function validate(string $nombre_zona): void
    {
        if (empty($nombre_zona)) {
            throw new \InvalidArgumentException('NombreZona cannot be empty');
        }
    }

    /**
     * Get the nombre_zona value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another NombreZona object
     *
     * @param NombreZona $other
     * @return bool
     */
    public function equals(NombreZona $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the nombre_zona
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
