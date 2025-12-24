<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for language
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class Idioma
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $idioma
     * @throws \InvalidArgumentException If the language is invalid
     */
    public function __construct(string $idioma)
    {
        $this->validate($idioma);
        $this->value = $idioma;
    }

    /**
     * Validate the language
     *
     * @param string $idioma
     * @throws \InvalidArgumentException If the language is invalid
     */
    private function validate(string $idioma): void
    {
        if (empty($idioma)) {
            throw new \InvalidArgumentException('Language cannot be empty');
        }

        // Add more validation rules if needed (e.g., check if it's a valid ISO language code)
    }

    /**
     * Get the language value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another Idioma object
     *
     * @param Idioma $other
     * @return bool
     */
    public function equals(Idioma $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the language
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}