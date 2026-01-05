<?php

namespace src\zonassacd\domain\value_objects;

/**
 * Value object for nombre_grupo
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 2026-01-01
 */
final class NombreGrupoZona
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $nombre_grupo
     * @throws \InvalidArgumentException If the nombre_grupo is invalid
     */
    public function __construct(string $nombre_grupo)
    {
        $this->validate($nombre_grupo);
        $this->value = $nombre_grupo;
    }

    /**
     * Validate the nombre_grupo
     *
     * @param string $nombre_grupo
     * @throws \InvalidArgumentException If the nombre_grupo is invalid
     */
    private function validate(string $nombre_grupo): void
    {
        if (empty($nombre_grupo)) {
            throw new \InvalidArgumentException('NombreGrupoZona cannot be empty');
        }
    }

    /**
     * Get the nombre_grupo value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another NombreGrupoZona object
     *
     * @param NombreGrupoZona $other
     * @return bool
     */
    public function equals(NombreGrupoZona $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the nombre_grupo
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
