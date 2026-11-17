<?php

declare(strict_types=1);

namespace src\devel_codegen\infrastructure\persistence;

use InvalidArgumentException;
use PDO;
use src\shared\infrastructure\GlobalPdo;

/**
 * Estructura de una tabla de PostgreSQL para el generador de código (`frontend/devel_codegen`):
 * clave primaria y columnas (tipo, longitud, not null y valor por defecto).
 */
final class TablaEstructuraPg
{
    /** base de datos del formulario => PDO global */
    private const PDO_DE = [
        'tramity' => 'oDBT',
        'davical' => 'oDBDavical',
        'comun' => 'oDBC',
        'sv' => 'oDB',
        'sf' => 'oDB',
        'sv-e' => 'oDBE',
    ];

    /**
     * @param \Closure(string): PDO|null $pdo PDO de la base de datos (por defecto, el global)
     */
    public function __construct(private ?\Closure $pdo = null)
    {
    }

    /**
     * @param string $tabla `tabla` o `esquema.tabla` (sin esquema: `public`)
     * @return array{
     *     primary_key: list<string>,
     *     columnas: list<array{attnum: int, field: string, type: string, length: int, lengthvar: int, notnull: bool, default: string}>
     * }
     */
    public function leer(string $db, string $tabla): array
    {
        $global = self::PDO_DE[$db] ?? null;
        if ($global === null) {
            throw new InvalidArgumentException("base de datos desconocida: $db");
        }
        $pdo = $this->pdo !== null ? ($this->pdo)($db) : GlobalPdo::get($global);

        [$schema, $nombre] = str_contains($tabla, '.') ? explode('.', $tabla, 2) : ['public', $tabla];
        $regclass = str_contains($tabla, '.') ? '"' . $schema . '".' . $nombre : $nombre;

        return [
            'primary_key' => $this->clavePrimaria($pdo, $regclass),
            'columnas' => $this->columnas($pdo, $schema, $nombre),
        ];
    }

    /** @return list<string> */
    private function clavePrimaria(PDO $pdo, string $regclass): array
    {
        $st = $pdo->prepare('SELECT a.attname
            FROM pg_index i
            JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
            WHERE i.indrelid = CAST(:t AS regclass) AND i.indisprimary');
        if ($st === false || !$st->execute([':t' => $regclass])) {
            return [];
        }
        $campo = $st->fetchColumn();

        // Como antes: solo el primer campo de la clave.
        return is_string($campo) && $campo !== '' ? [$campo] : [];
    }

    /**
     * @return list<array{attnum: int, field: string, type: string, length: int, lengthvar: int, notnull: bool, default: string}>
     */
    private function columnas(PDO $pdo, string $schema, string $nombre): array
    {
        $st = $pdo->prepare('SELECT a.attnum, a.attname AS field, t.typname AS type, a.attlen AS length,
                a.atttypmod AS lengthvar, a.attnotnull AS notnull, pg_get_expr(d.adbin, d.adrelid) AS rowdefault
            FROM pg_catalog.pg_class c
            JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
            JOIN pg_catalog.pg_attribute a ON a.attrelid = c.oid
            JOIN pg_catalog.pg_type t ON t.oid = a.atttypid
            LEFT JOIN pg_catalog.pg_attrdef d ON d.adrelid = c.oid AND d.adnum = a.attnum
            WHERE c.relname = :tabla AND n.nspname = :schema AND a.attnum > 0
            ORDER BY a.attnum');
        if ($st === false || !$st->execute([':tabla' => $nombre, ':schema' => $schema])) {
            return [];
        }
        $out = [];
        while (($row = $st->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (!is_array($row)) {
                continue;
            }
            $out[] = [
                'attnum' => (int) $row['attnum'],
                'field' => (string) $row['field'],
                'type' => (string) $row['type'],
                'length' => (int) $row['length'],
                'lengthvar' => (int) $row['lengthvar'],
                'notnull' => $row['notnull'] === true || $row['notnull'] === 't' || $row['notnull'] === 1,
                'default' => is_string($row['rowdefault'] ?? null) ? $row['rowdefault'] : '',
            ];
        }

        return $out;
    }
}
