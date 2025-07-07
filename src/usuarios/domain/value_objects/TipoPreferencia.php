<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for preference type
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class TipoPreferencia
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $tipoPreferencia
     * @throws \InvalidArgumentException If the preference type is invalid
     */
    public function __construct(string $tipoPreferencia)
    {
        $this->validate($tipoPreferencia);
        $this->value = $tipoPreferencia;
    }

    /**
     * Validate the preference type
     *
     * @param string $tipoPreferencia
     * @throws \InvalidArgumentException If the preference type is invalid
     */
    private function validate(string $tipoPreferencia): void
    {
        if (empty($tipoPreferencia)) {
            throw new \InvalidArgumentException('Preference type cannot be empty');
        }

        // Add more validation rules if needed (e.g., check if it's a valid preference type)
    }

    /**
     * Get the preference type value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another TipoPreferencia object
     *
     * @param TipoPreferencia $other
     * @return bool
     */
    public function equals(TipoPreferencia $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the preference type
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}