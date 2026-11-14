<?php

declare(strict_types=1);

namespace Tests\support;

use PDO;

/**
 * Ejecuta un bloque dentro de una transacción en cada conexión de la BD, apunta en qué tablas ha
 * escrito y lo deshace todo (ROLLBACK).
 *
 * Sirve para comprobar que algo que debería ser solo lectura (un controlador `*_data`, una
 * pantalla) no cambia datos, sin que la comprobación toque la BD de desarrollo. Las tablas
 * temporales no cuentan. `nextval()` no se ve (no es una escritura en una tabla) ni se deshace.
 *
 * Límite: si el bloque abre su propia transacción con `PDO::beginTransaction()`, PDO lanza
 * «There is already an active transaction»; ese código no se puede medir así.
 */
trait DetectaEscriturasEnBd
{
    /**
     * @param callable():void $bloque
     * @return array{escrituras: list<string>, abortadas: list<string>}
     *         `escrituras`: «BD:esquema.tabla (ins/upd/del)» de cada tabla permanente tocada.
     *         `abortadas`: conexiones cuya transacción quedó abortada por un error de SQL (lo que
     *         el bloque haya hecho después en esa conexión no se ha ejecutado).
     */
    protected function escriturasDe(callable $bloque): array
    {
        $conexiones = $this->conexionesAbiertas();
        foreach ($conexiones as $pdo) {
            // `pg_stat_xact_user_tables` enseña lo que la conexión aún no ha volcado a las
            // estadísticas, que puede incluir transacciones anteriores: se vuelca antes de empezar.
            $pdo->query('SELECT pg_stat_force_next_flush()');
            $pdo->exec('BEGIN');
        }
        $escrituras = [];
        $abortadas = [];
        try {
            $bloque();
        } finally {
            foreach ($conexiones as $nombre => $pdo) {
                try {
                    $st = $pdo->query(
                        "SELECT current_database() || ':' || schemaname || '.' || relname
                                || ' (' || n_tup_ins || '/' || n_tup_upd || '/' || n_tup_del || ')'
                           FROM pg_stat_xact_user_tables
                          WHERE n_tup_ins + n_tup_upd + n_tup_del > 0 AND schemaname !~ '^pg_temp'
                          ORDER BY 1"
                    );
                    foreach ($st === false ? [] : $st->fetchAll(PDO::FETCH_COLUMN) as $tabla) {
                        $escrituras[] = (string) $tabla;
                    }
                } catch (\PDOException) {
                    $abortadas[] = $nombre;
                }
                try {
                    $pdo->exec('ROLLBACK');
                } catch (\PDOException) {
                    // sin transacción abierta: el bloque la cerró por su cuenta
                }
            }
        }

        return ['escrituras' => array_values(array_unique($escrituras)), 'abortadas' => $abortadas];
    }

    /**
     * Las PDO de PostgreSQL de `$GLOBALS['oDB…']`, sin repetir (varios nombres comparten conexión).
     *
     * @return array<string, PDO>
     */
    private function conexionesAbiertas(): array
    {
        $vistas = [];
        $conexiones = [];
        foreach ($GLOBALS as $nombre => $valor) {
            if (!is_string($nombre) || !str_starts_with($nombre, 'oDB') || !$valor instanceof PDO) {
                continue;
            }
            $id = spl_object_id($valor);
            if (isset($vistas[$id]) || $valor->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'pgsql') {
                continue;
            }
            $vistas[$id] = true;
            $conexiones[$nombre] = $valor;
        }

        return $conexiones;
    }
}
