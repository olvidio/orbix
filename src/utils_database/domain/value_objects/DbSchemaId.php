<?php

namespace src\utils_database\domain\value_objects;

final class DbSchemaId
{
    /**
     * Ids reservados para el esquema «resto» (restov, restof, …) en db_idschema.
     * No son positivos por diseño, para no colisionar con el rango normal de delegaciones.
     */
    private const IDS_RESTO = [-1001, -2001, -3001];

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (in_array($value, self::IDS_RESTO, true)) {
            return;
        }
        if ($value <= 0) {
            throw new \InvalidArgumentException(
                'DbSchemaId must be a positive integer or one of the reserved resto ids: -1001, -2001, -3001',
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(DbSchemaId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);
        if ($value === '' || !preg_match('/^-?\d+$/', $value)) {
            throw new \InvalidArgumentException('DbSchemaId string must be a signed decimal integer');
        }

        return new self((int) $value);
    }
}
