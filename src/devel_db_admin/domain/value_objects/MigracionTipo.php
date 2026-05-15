<?php

declare(strict_types=1);

namespace src\devel_db_admin\domain\value_objects;

use InvalidArgumentException;

final class MigracionTipo
{
    public const ESTRUCTURA = 'estructura';
    public const DATOS = 'datos';

    private const VALID = [
        self::ESTRUCTURA,
        self::DATOS,
    ];

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (!in_array($value, self::VALID, true)) {
            throw new InvalidArgumentException(sprintf('Tipo de migracion no valido: %s', $value));
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return list<string>
     */
    public static function validValues(): array
    {
        return self::VALID;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
