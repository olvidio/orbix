<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence;

use PDO;

/**
 * Utilidades sobre esquemas de PostgreSQL y sobre la convención de nombres de
 * esquema de Orbix (`H-dlb` en comun, `H-dlbv` en sv, `H-dlbf` en sf).
 *
 * Vive en `shared` porque la usan tanto las migraciones multi-esquema
 * ({@see \src\devel_db_admin\application\MigracionEjecucionUtiles}, que delega
 * aquí) como la reconciliación de las tablas copia
 * ({@see \src\shared\application\copias\ReconciliadorCopia}). Tener una única
 * definición evita que las dos listas de esquemas se separen con el tiempo.
 */
final class EsquemaPg
{
    /** Esquemas «resto», comunes a todas las dl: no tienen tablas `*_dl` propias. */
    public static function esEsquemaResto(string $schema): bool
    {
        $s = strtolower($schema);

        return $s === 'resto' || $s === 'restov' || $s === 'restof';
    }

    /**
     * Esquemas raíz de región STGR en comun (H-H, M-M): no usan tablas `*_dl`
     * propias y quedan fuera del comodín `*` en migraciones multi-esquema.
     */
    public static function esEsquemaRegionStgrComun(string $schema): bool
    {
        return $schema === 'H-H' || $schema === 'M-M';
    }

    /** ¿Existe el esquema en la base a la que apunta esta conexión? */
    public static function existeEnPostgres(PDO $pdo, string $schema): bool
    {
        $sql = 'SELECT 1 FROM pg_catalog.pg_namespace WHERE nspname = ? LIMIT 1';
        $stmt = $pdo->prepare($sql);
        if ($stmt === false) {
            return false;
        }
        $stmt->execute([$schema]);

        return (bool) $stmt->fetchColumn();
    }
}
