<?php

declare(strict_types=1);

namespace src\devel_db_admin\domain\value_objects;

use InvalidArgumentException;

final class MigracionDatabase
{
    public const COMUN = 'comun';
    public const COMUN_SELECT = 'comun_select';
    public const SV = 'sv';
    public const SV_E = 'sv-e';
    public const SV_E_SELECT = 'sv-e_select';

    private const VALID = [
        self::COMUN,
        self::COMUN_SELECT,
        self::SV,
        self::SV_E,
        self::SV_E_SELECT,
    ];

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (!in_array($value, self::VALID, true)) {
            throw new InvalidArgumentException(sprintf('Database de migracion no valida: %s', $value));
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
