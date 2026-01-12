<?php

namespace src\usuarios\domain\value_objects;

/**
 * Value object for PAU type
 *
 * @package orbix
 * @subpackage model
 * @author AI Assistant
 * @version 1.0
 * @created 16/6/2023
 */
final class PauType
{
    // PAU constants
    public const PAU_NONE = 'none'; // Ninguno
    public const PAU_CDC = 'cdc'; // Casa
    public const PAU_CTR = 'ctr'; // Centro
    public const PAU_NOM = 'nom'; // Persona
    public const PAU_SACD = 'sacd'; // Sacd

    public static function getArrayPau(): array
    {
        $a_pau = [
            self::PAU_NONE,
            self::PAU_CDC,
            self::PAU_CTR,
            self::PAU_NOM,
            self::PAU_SACD,
        ];
        return $a_pau;
    }

    // ---------------------------------------------------------------------------
    private string $value;

    /**
     * Constructor
     *
     * @param string $pauType
     * @throws \InvalidArgumentException If the PAU type is invalid
     */
    public function __construct(string $pauType)
    {
        $this->validate($pauType);
        $this->value = $pauType;
    }

    /**
     * Validate the PAU type
     *
     * @param string $pauType
     * @throws \InvalidArgumentException If the PAU type is invalid
     */
    private function validate(string $pauType): void
    {
        if (empty($pauType)) {
            throw new \InvalidArgumentException('PAU type cannot be empty');
        }

        $validPauTypes = [
            self::PAU_NONE,
            self::PAU_CDC,
            self::PAU_CTR,
            self::PAU_NOM,
            self::PAU_SACD
        ];

        if (!in_array(strtolower($pauType), $validPauTypes)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid PAU type. Valid types are: %s', implode(', ', $validPauTypes))
            );
        }
    }

    /**
     * Get the PAU type value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another PauType object
     *
     * @param PauType $other
     * @return bool
     */
    public function equals(PauType $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation of the PAU type
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}