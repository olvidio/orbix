<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use PDO;
use PDOException;
use Throwable;

/**
 * Reglas de ejecución de migraciones SQL multi-esquema (comodín *.).
 */
final class MigracionEjecucionUtiles
{
    /**
     * Esquemas «resto»: convención de tablas distinta; se excluyen de la expansión *.
     */
    public static function esEsquemaResto(string $schema): bool
    {
        $s = strtolower($schema);

        return $s === 'resto' || $s === 'restov' || $s === 'restof';
    }

    /**
     * Esquemas raíz de región STGR en comun (H-H, M-M): no usan tablas *_dl propias
     * y quedan fuera del comodín * en migraciones multi-esquema.
     */
    public static function esEsquemaRegionStgrComun(string $schema): bool
    {
        return $schema === 'H-H' || $schema === 'M-M';
    }

    /**
     * PostgreSQL: SQLSTATE 3F000 = invalid_schema_name ("schema X does not exist").
     * No confundir con 42P01 (relación inexistente cuando el esquema sí existe).
     */
    public static function esErrorEsquemaInexistente(Throwable $e): bool
    {
        if (!$e instanceof PDOException) {
            return false;
        }

        return (string) ($e->errorInfo[0] ?? '') === '3F000';
    }

    /**
     * Comprueba si el namespace existe en la base actual (catalogo PostgreSQL).
     */
    public static function esquemaExisteEnPostgres(PDO $pdo, string $schema): bool
    {
        $sql = 'SELECT 1 FROM pg_catalog.pg_namespace WHERE nspname = ? LIMIT 1';
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            return false;
        }
        $stmt->execute([$schema]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Errores que en la practica equivalen a «este esquema no existe en esta BD» y pueden omitirse
     * al iterar el comodin, sin confundir con tabla mal nombrada en un esquema que si existe.
     */
    public static function esOmitiblePorAusenciaDeEsquema(Throwable $e, PDO $pdo, string $schema): bool
    {
        if (self::esErrorEsquemaInexistente($e)) {
            return true;
        }
        if (!$e instanceof PDOException) {
            return false;
        }
        if ((string) ($e->errorInfo[0] ?? '') !== '42P01') {
            return false;
        }

        return !self::esquemaExisteEnPostgres($pdo, $schema);
    }
}
