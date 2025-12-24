<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for role name
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class RoleName
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $roleName
     * @throws \InvalidArgumentException If the role name is invalid
     */
    public function __construct(string $roleName)
    {
        $this->validate($roleName);
        $this->value = $roleName;
    }

    /**
     * Validate the role name
     *
     * @param string $roleName
     * @throws \InvalidArgumentException If the role name is invalid
     */
    private function validate(string $roleName): void
    {
        if (empty($roleName)) {
            throw new \InvalidArgumentException('Role name cannot be empty');
        }

        // Add more validation rules if needed
    }

    /**
     * Get the role name value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another RoleName object
     *
     * @param RoleName $other
     * @return bool
     */
    public function equals(RoleName $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the role name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}