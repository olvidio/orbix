<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for locale name
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class NombreLocale
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $nombreLocale
     * @throws \InvalidArgumentException If the locale name is invalid
     */
    public function __construct(string $nombreLocale)
    {
        $this->validate($nombreLocale);
        $this->value = $nombreLocale;
    }

    /**
     * Validate the locale name
     *
     * @param string $nombreLocale
     * @throws \InvalidArgumentException If the locale name is invalid
     */
    private function validate(string $nombreLocale): void
    {
        if (empty($nombreLocale)) {
            throw new \InvalidArgumentException('Locale name cannot be empty');
        }

        // Add more validation rules if needed
    }

    /**
     * Get the locale name value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another NombreLocale object
     *
     * @param NombreLocale $other
     * @return bool
     */
    public function equals(NombreLocale $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the locale name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}