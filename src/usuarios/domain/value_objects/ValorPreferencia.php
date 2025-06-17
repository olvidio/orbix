<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for preference value
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class ValorPreferencia
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $valorPreferencia
     * @throws \InvalidArgumentException If the preference value is invalid
     */
    public function __construct(string $valorPreferencia)
    {
        $this->validate($valorPreferencia);
        $this->value = $valorPreferencia;
    }

    /**
     * Validate the preference value
     *
     * @param string $valorPreferencia
     * @throws \InvalidArgumentException If the preference value is invalid
     */
    private function validate(string $valorPreferencia): void
    {
        // Preference value can be empty, so no validation for emptiness

        // Add more validation rules if needed
    }

    /**
     * Get the preference value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another ValorPreferencia object
     *
     * @param ValorPreferencia $other
     * @return bool
     */
    public function equals(ValorPreferencia $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the preference value
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}