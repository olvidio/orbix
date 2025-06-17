<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for password
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class Password
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $password
     * @throws \InvalidArgumentException If the password is invalid
     */
    public function __construct(string $password)
    {
        $this->validate($password);
        $this->value = $password;
    }

    /**
     * Validate the password
     *
     * @param string $password
     * @throws \InvalidArgumentException If the password is invalid
     */
    private function validate(string $password): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('Password cannot be empty');
        }

        // Add more validation rules if needed (e.g., minimum length, complexity)
    }

    /**
     * Get the password value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Hash the password
     *
     * @return string
     */
    public function hash(): string
    {
        return password_hash($this->value, PASSWORD_DEFAULT);
    }

    /**
     * Verify if a plain password matches this password
     *
     * @param string $plainPassword
     * @param string $hashedPassword
     * @return bool
     */
    public static function verify(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * Compare with another Password object
     *
     * @param Password $other
     * @return bool
     */
    public function equals(Password $other): bool
    {
        return $this->value === $other->value;
    }
}