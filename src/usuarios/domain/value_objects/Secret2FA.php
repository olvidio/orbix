<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for two-factor authentication secret
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class Secret2FA
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $secret
     * @throws \InvalidArgumentException If the 2FA secret is invalid
     */
    public function __construct(string $secret)
    {
        $this->validate($secret);
        $this->value = $secret;
    }

    /**
     * Validate the 2FA secret
     *
     * @param string $secret
     * @throws \InvalidArgumentException If the 2FA secret is invalid
     */
    private function validate(string $secret): void
    {
        if (empty($secret)) {
            throw new \InvalidArgumentException('2FA secret cannot be empty');
        }

        // Add more validation rules if needed (e.g., base32 encoding check)
    }

    /**
     * Get the 2FA secret value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another Secret2FA object
     *
     * @param Secret2FA $other
     * @return bool
     */
    public function equals(Secret2FA $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the 2FA secret
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}