<?php

namespace src\actividadessacd\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;

/**
 * Reordena un sacd en el listado de cargos sacd de una actividad.
 *
 * Intercambia `id_nom` con el vecino superior (`mas`) o inferior (`menos`)
 * ordenando por `id_cargo` natural (cargos sacd consecutivos). La rotacion
 * se hace sobre `id_nom`, no sobre `id_cargo`, porque los `id_cargo`
 * estan enumerados y se usan en la UI para identificar el hueco.
 *
 * Sucesor de la funcion suelta `ordena()` del dispatcher legacy
 * `apps/actividadessacd/controller/activ_sacd_ajax.php`.
 */
final class SacdReordenar
{
    public static function execute(array $input): string
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $id_nom = (int)($input['id_nom'] ?? 0);
        $direccion = (string)($input['num_orden'] ?? '');

        if ($id_activ <= 0 || $id_nom <= 0) {
            return _("faltan parametros id_activ / id_nom");
        }
        if ($direccion !== 'mas' && $direccion !== 'menos') {
            return _("direccion de orden incorrecta (mas / menos)");
        }

        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $cCargos = $ActividadCargoRepository->getActividadCargos(
            ['id_activ' => $id_activ, 'id_cargo' => $txt_where_cargos],
            ['id_cargo' => 'IN']
        );
        if (!is_array($cCargos)) {
            $cCargos = [];
        }

        $errors = '';
        $i_max = count($cCargos);
        for ($i = 0; $i < $i_max; $i++) {
            if ((int)$cCargos[$i]->getId_nom() !== $id_nom) {
                continue;
            }
            if ($direccion === 'mas' && $i >= 1) {
                $anterior_id_nom = (int)$cCargos[$i - 1]->getId_nom();
                if ($anterior_id_nom === 0) {
                    break;
                }
                $cCargos[$i - 1]->setId_nom($id_nom);
                if ($ActividadCargoRepository->Guardar($cCargos[$i - 1]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
                $cCargos[$i]->setId_nom($anterior_id_nom);
                if ($ActividadCargoRepository->Guardar($cCargos[$i]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
            } elseif ($direccion === 'menos' && $i < ($i_max - 1)) {
                $post_id_nom = (int)$cCargos[$i + 1]->getId_nom();
                if ($post_id_nom === 0) {
                    break;
                }
                $cCargos[$i + 1]->setId_nom($id_nom);
                if ($ActividadCargoRepository->Guardar($cCargos[$i + 1]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
                $cCargos[$i]->setId_nom($post_id_nom);
                if ($ActividadCargoRepository->Guardar($cCargos[$i]) === false) {
                    $errors .= _("hay un error, no se ha guardado") . ' ';
                }
            }
            break;
        }
        return trim($errors);
    }
}
