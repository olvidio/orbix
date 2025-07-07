<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for email
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class Email
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $email
     * @throws \InvalidArgumentException If the email is invalid
     */
    public function __construct(string $email)
    {
        $this->validate($email);
        $this->value = $email;
    }

    /**
     * Validate the email
     *
     * @param string $email
     * @throws \InvalidArgumentException If the email is invalid
     */
    private function validate(string $email): void
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    /**
     * Get the email value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another Email object
     *
     * @param Email $other
     * @return bool
     */
    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the email
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}