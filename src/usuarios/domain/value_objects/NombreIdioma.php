<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for language name
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class NombreIdioma
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $nombreIdioma
     * @throws \InvalidArgumentException If the language name is invalid
     */
    public function __construct(string $nombreIdioma)
    {
        $this->validate($nombreIdioma);
        $this->value = $nombreIdioma;
    }

    /**
     * Validate the language name
     *
     * @param string $nombreIdioma
     * @throws \InvalidArgumentException If the language name is invalid
     */
    private function validate(string $nombreIdioma): void
    {
        if (empty($nombreIdioma)) {
            throw new \InvalidArgumentException('Language name cannot be empty');
        }

        // Add more validation rules if needed
    }

    /**
     * Get the language name value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another NombreIdioma object
     *
     * @param NombreIdioma $other
     * @return bool
     */
    public function equals(NombreIdioma $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the language name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}