<?php

namespace src\actividadestudios\application;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use src\profesores\domain\ProfesorActividad;

/**
 * Devuelve los datos (`id`, `opciones`, `selected`, `blanco`) para construir
 * un `<select>` de profesores en el form de `ActividadAsignatura`.
 *
 * Sustituye al legacy `apps/actividadestudios/controller/lista_profesores_ajax.php`
 * que devolvia HTML directamente. Ahora se devuelve JSON y el cliente
 * construye el desplegable con `fnjs_construir_desplegable`.
 *
 * `salida` controla el filtro:
 * - `asignatura` → profesores que imparten la asignatura (`id_asignatura`).
 * - `dl` → profesores y asistentes de la actividad (`id_activ`).
 * - `todos` → todos los profesores stgr (catalogo publico).
 */
final class ProfesoresDesplegableData
{
    public static function execute(array $input): array
    {
        $salida = (string) ($input['salida'] ?? '');
        $id_asignatura = (int) ($input['id_asignatura'] ?? 0);
        $id_activ = (int) ($input['id_activ'] ?? 0);

        switch ($salida) {
            case 'asignatura':
                $ProfesorAsignaturaService = $GLOBALS['container']->get(ProfesorAsignaturaService::class);
                $aOpciones = $ProfesorAsignaturaService->getArrayTodosProfesoresAsignatura(new AsignaturaId($id_asignatura));
                break;
            case 'dl':
                $ProfesorActividad = new ProfesorActividad();
                $aOpciones = $ProfesorActividad->getArrayProfesoresActividad([$id_activ]);
                break;
            case 'todos':
                $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
                $aOpciones = $ProfesorStgrService->getArrayProfesoresPub();
                break;
            default:
                $aOpciones = [];
        }

        return [
            'id' => 'id_profesor',
            'opciones' => $aOpciones,
            'blanco' => true,
            'val_blanco' => '',
            'selected' => -1,
        ];
    }
}
