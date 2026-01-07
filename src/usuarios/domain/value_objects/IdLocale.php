<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for locale identifier
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class IdLocale
{
    private string $value;

    /**
     * Constructor
     *
     * @param string $idLocale
     * @throws \InvalidArgumentException If the locale identifier is invalid
     */
    public function __construct(string $idLocale)
    {
        $this->validate($idLocale);
        $this->value = $idLocale;
    }

    /**
     * Validate the locale identifier
     *
     * @param string $idLocale
     * @throws \InvalidArgumentException If the locale identifier is invalid
     */
    private function validate(string $idLocale): void
    {
        if (empty($idLocale)) {
            throw new \InvalidArgumentException('Locale identifier cannot be empty');
        }

        // Add more validation rules if needed (e.g., format check like 'en_US', 'es_ES', etc.)
    }

    /**
     * Get the locale identifier value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another IdLocale object
     *
     * @param IdLocale $other
     * @return bool
     */
    public function equals(IdLocale $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the locale identifier
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }

}