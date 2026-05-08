<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\infrastructure\persistence\postgresql\DBPropiedades;

/**
 * Datos de UI basados en {@see DBPropiedades} para pantallas de `devel_db_admin`.
 */
final class DbPropiedadesFormData
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>|array{error: string}
     */
    public static function build(array $input): array
    {
        $op = (string)($input['op'] ?? '');
        $dbp = new DBPropiedades();

        return match ($op) {
            'apptables_esquemas' => self::apptablesEsquemas($dbp, $input),
            'db_que_esquema_ref' => self::dbQueEsquemaRef($dbp),
            'db_cambiar_nombre_esquemas' => self::dbCambiarNombreEsquemas($dbp),
            'db_mover_tablas' => self::dbMoverTablas($dbp),
            'db_mover_esquemas_con_tabla' => self::dbMoverEsquemasConTabla($dbp, $input),
            default => ['error' => 'op no válida'],
        };
    }

    /**
     * @param array<string, mixed> $input
     * @return array{oDesplEsquemas: string|false}
     */
    private static function apptablesEsquemas(DBPropiedades $dbp, array $input): array
    {
        $default = (string)($input['default_esquema'] ?? '');

        return [
            'oDesplEsquemas' => $dbp->posibles_esquemas($default, false),
        ];
    }

    /**
     * @return array{oEsquemaRef: string|false}
     */
    private static function dbQueEsquemaRef(DBPropiedades $dbp): array
    {
        $dbp->setBlanco(true);

        return [
            'oEsquemaRef' => $dbp->posibles_esquemas(''),
        ];
    }

    /**
     * @return array{oEsquemaRef: string|false, a_posibles_esquemas: array<string, string>}
     */
    private static function dbCambiarNombreEsquemas(DBPropiedades $dbp): array
    {
        $dbp->setBlanco(true);
        $opts = $dbp->array_posibles_esquemas(true);

        return [
            'oEsquemaRef' => $dbp->posibles_esquemas(''),
            'a_posibles_esquemas' => is_array($opts) ? $opts : [],
        ];
    }

    /**
     * @return array{desplTablas: string|false}
     */
    private static function dbMoverTablas(DBPropiedades $dbp): array
    {
        $dbp->setBlanco(true);

        return [
            'desplTablas' => $dbp->posibles_tablas(),
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{a_esquemas_con_tabla: list<string>}
     */
    private static function dbMoverEsquemasConTabla(DBPropiedades $dbp, array $input): array
    {
        $tabla = (string)($input['tabla'] ?? '');
        $raw = $dbp->array_esquemas_con_tabla($tabla);
        $list = is_array($raw) ? array_values($raw) : [];

        return [
            'a_esquemas_con_tabla' => $list,
        ];
    }
}
