<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for PAU identifier
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class IdPau
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $idPau
     * @throws \InvalidArgumentException If the PAU identifier is invalid
     */
    public function __construct(string $idPau)
    {
        $this->validate($idPau);
        $this->value = $idPau;
    }

    /**
     * Validate the PAU identifier
     *
     * @param string $idPau
     * @throws \InvalidArgumentException If the PAU identifier is invalid
     */
    private function validate(string $idPau): void
    {
        if (empty($idPau)) {
            throw new \InvalidArgumentException('PAU identifier cannot be empty');
        }

        // Add more validation rules if needed
    }

    /**
     * Get the PAU identifier value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another IdPau object
     *
     * @param IdPau $other
     * @return bool
     */
    public function equals(IdPau $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the PAU identifier
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}