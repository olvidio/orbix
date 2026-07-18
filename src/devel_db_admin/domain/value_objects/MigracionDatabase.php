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
    /** BD única sf: tablas de sv y de sv-e; sin réplica *_select. */
    public const SF = 'sf';

    private const VALID = [
        self::COMUN,
        self::COMUN_SELECT,
        self::SV,
        self::SV_E,
        self::SV_E_SELECT,
        self::SF,
    ];

    /** Serie sv (comun + sv + sv-e y réplicas). */
    public const SERIE_SV = 'sv';

    /** Serie sf (solo BD sf). */
    public const SERIE_SF = 'sf';

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

    /**
     * Destinos que pertenecen a la serie (listado / ejecución según sfsv).
     *
     * @return list<string>
     */
    public static function databasesDeSerie(string $serie): array
    {
        return match ($serie) {
            self::SERIE_SF => [self::SF],
            default => [
                self::COMUN,
                self::COMUN_SELECT,
                self::SV_E,
                self::SV_E_SELECT,
                self::SV,
            ],
        };
    }

    /**
     * Sufijos de fichero `__….sql` visibles en la serie.
     *
     * @return list<string>
     */
    public static function archivosDeSerie(string $serie): array
    {
        return match ($serie) {
            self::SERIE_SF => [self::SF],
            default => [self::COMUN, self::SV_E, self::SV],
        };
    }

    public static function perteneceASerie(string $database, string $serie): bool
    {
        return in_array($database, self::databasesDeSerie($serie), true);
    }
}
