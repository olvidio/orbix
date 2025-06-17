<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for user name
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class NombreUsuario
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $nombreUsuario
     * @throws \InvalidArgumentException If the user name is invalid
     */
    public function __construct(string $nombreUsuario)
    {
        $this->validate($nombreUsuario);
        $this->value = $nombreUsuario;
    }

    /**
     * Validate the user name
     *
     * @param string $nombreUsuario
     * @throws \InvalidArgumentException If the user name is invalid
     */
    private function validate(string $nombreUsuario): void
    {
        if (empty($nombreUsuario)) {
            throw new \InvalidArgumentException('User name cannot be empty');
        }

        // Add more validation rules if needed
    }

    /**
     * Get the user name value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another NombreUsuario object
     *
     * @param NombreUsuario $other
     * @return bool
     */
    public function equals(NombreUsuario $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the user name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}