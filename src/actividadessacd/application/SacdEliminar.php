<?php

namespace src\actividadessacd\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;

/**
 * Elimina un sacd ({id_activ, id_cargo}) de una actividad, incluyendo
 * la fila de `Asistencia` {id_activ, id_nom} asociada (si existe).
 *
 * Sucesor de la rama `orden` + `num_orden='borrar'` del dispatcher legacy
 * `apps/actividadessacd/controller/activ_sacd_ajax.php`. En el legacy se
 * usaban los metodos mal escritos `finsById` y
 * `DBEliminar()` directamente en la entidad; aqui se arregla al contrato
 * estandar del repositorio (`findById` + `Eliminar`).
 */
final class SacdEliminar
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $id_cargo = (int)($input['id_cargo'] ?? 0);
        $id_nom = (int)($input['id_nom'] ?? 0);

        if ($id_activ <= 0 || $id_cargo <= 0) {
            return _("no se sabe cual borrar");
        }

        $errors = '';

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $cActCargo = $ActividadCargoRepository->getActividadCargos(
            ['id_activ' => $id_activ, 'id_cargo' => $id_cargo]
        );
        if (is_array($cActCargo) && count($cActCargo) >= 1) {
            if ($ActividadCargoRepository->Eliminar($cActCargo[0]) === false) {
                $errors .= _("hay un error, no se ha eliminado el cargo") . ' ';
            }
        }

        // Tambien la asistencia, si existe.
        if ($id_nom > 0) {
            $AsistenteDlRepository = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
            $oAsisActiv = $AsistenteDlRepository->findById($id_activ, $id_nom);
            if ($oAsisActiv !== null) {
                if ($AsistenteDlRepository->Eliminar($oAsisActiv) === false) {
                    $errors .= _("hay un error, no se ha eliminado la asistencia") . ' ';
                }
            }
        }

        return trim($errors);
    }
}
