<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\application\services\RegionDropdown;

/**
 * Datos de UI basados en {@see DBPropiedades} para pantallas de `devel_db_admin`.
 */
final class DbPropiedadesFormData
{
    public function __construct(
        private readonly RegionDropdown $regionDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>|array{error: string}
     */
    public function build(array $input): array
    {
        $opVal = $input['op'] ?? '';
        $op = is_scalar($opVal) ? (string) $opVal : '';
        $dbp = new DBPropiedades();

        return match ($op) {
            'apptables_esquemas' => $this->apptablesEsquemas($dbp, $input),
            'db_que_esquema_ref' => $this->dbQueEsquemaRef($dbp),
            'db_cambiar_nombre_esquemas' => $this->dbCambiarNombreEsquemas($dbp),
            'db_absorber_esquema_que' => $this->dbAbsorberEsquemaQue($dbp),
            'db_mover_tablas' => $this->dbMoverTablas($dbp),
            'db_mover_esquemas_con_tabla' => $this->dbMoverEsquemasConTabla($dbp, $input),
            default => ['error' => 'op no válida'],
        };
    }

    /**
     * @param array<string, mixed> $input
     * @return array{oDesplEsquemas: string|false}
     */
    private function apptablesEsquemas(DBPropiedades $dbp, array $input): array
    {
        $defaultVal = $input['default_esquema'] ?? '';
        $default = is_scalar($defaultVal) ? (string) $defaultVal : '';

        return [
            'oDesplEsquemas' => $dbp->posibles_esquemas($default, false),
        ];
    }

    /**
     * @return array{oEsquemaRef: string|false, a_opciones_regiones: array<string, string>}
     */
    private function dbQueEsquemaRef(DBPropiedades $dbp): array
    {
        $dbp->setBlanco(true);

        return [
            'oEsquemaRef' => $dbp->posibles_esquemas(''),
            'a_opciones_regiones' => $this->regionDropdown->activasOrdenNombre(),
        ];
    }

    /**
     * @return array{a_esquemas_union: array<string, string>, a_opciones_regiones: array<string, string>}
     */
    private function dbCambiarNombreEsquemas(DBPropiedades $dbp): array
    {
        return [
            'a_esquemas_union' => $dbp->array_esquemas_union_importar(),
            'a_opciones_regiones' => $this->regionDropdown->activasOrdenNombre(),
        ];
    }

    /**
     * Solo desplegables de esquema para la pantalla de absorber esquema (`db_absorber_esquema_que.php`).
     *
     * @return array{a_posibles_esquemas: array<string, string>}
     */
    private function dbAbsorberEsquemaQue(DBPropiedades $dbp): array
    {
        $dbp->setBlanco(true);
        $opts = $dbp->array_posibles_esquemas(true);
        $mapped = [];
        if (is_array($opts)) {
            foreach ($opts as $k => $v) {
                $key = (string) $k;
                if ($key === '') {
                    continue;
                }
                $mapped[$key] = is_scalar($v) ? (string) $v : $key;
            }
        }

        return [
            'a_posibles_esquemas' => $mapped,
        ];
    }

    /**
     * @return array{desplTablas: string|false}
     */
    private function dbMoverTablas(DBPropiedades $dbp): array
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
    private function dbMoverEsquemasConTabla(DBPropiedades $dbp, array $input): array
    {
        $tablaVal = $input['tabla'] ?? '';
        $tabla = is_scalar($tablaVal) ? (string) $tablaVal : '';
        $raw = $dbp->array_esquemas_con_tabla($tabla);
        $list = [];
        if (is_array($raw)) {
            foreach ($raw as $value) {
                if (is_scalar($value) && (string) $value !== '') {
                    $list[] = (string) $value;
                }
            }
        }

        return [
            'a_esquemas_con_tabla' => $list,
        ];
    }
}
