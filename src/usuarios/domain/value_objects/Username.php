<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for username
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class Username
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $username
     * @throws \InvalidArgumentException If the username is invalid
     */
    public function __construct(string $username)
    {
        $this->validate($username);
        $this->value = $username;
    }

    /**
     * Validate the username
     *
     * @param string $username
     * @throws \InvalidArgumentException If the username is invalid
     */
    private function validate(string $username): void
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('Username cannot be empty');
        }

        // Add more validation rules if needed
    }

    /**
     * Get the username value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another Username object
     *
     * @param Username $other
     * @return bool
     */
    public function equals(Username $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the username
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}