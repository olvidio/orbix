<?php

namespace src\notas\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\value_objects\NotaEpoca;

/**
 * Busca un acta por su numero abreviado (tal como lo teclea el usuario)
 * y devuelve los datos asociados (asignatura, nivel, actividad, fecha,
 * epoca).
 *
 * Si no encuentra ninguna coincidencia unica, devuelve
 * `['id_asignatura' => 'no']` para preservar el contrato historico
 * consumido por `form_1011.phtml`.
 */
final class BuscarActaData
{
    public static function execute(array $input): array
    {
        $acta = (string)($input['acta'] ?? '');

        $matches = [];
        preg_match("/^(\d*)(\/)?(\d*)/", $acta, $matches);
        if (!empty($matches[1])) {
            $mi_dele = ConfigGlobal::mi_delef();
            $acta = empty($matches[3])
                ? "$mi_dele " . $matches[1] . '/' . date('y')
                : "$mi_dele $acta";
        }

        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $cActas = $ActaRepository->getActas(['acta' => $acta]);

        if (count($cActas) !== 1) {
            return ['id_asignatura' => 'no'];
        }

        $oActa = $cActas[0];
        $id_asignatura = $oActa->getId_asignatura();
        $id_activ = $oActa->getId_activ();

        if (!empty($id_activ)) {
            $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
            $oActividad = $ActividadAllRepository->findById($id_activ);
            $nom_activ = $oActividad?->getNom_activ() ?? '';
            $id_tipo_actividad = $oActividad?->getId_tipo_activ();
            $epoca = $id_tipo_actividad === 132500 ? NotaEpoca::EPOCA_INVIERNO : NotaEpoca::EPOCA_CA;
        } else {
            $nom_activ = '';
            $epoca = NotaEpoca::EPOCA_OTRO;
        }

        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
        }

        return [
            'id_asignatura' => (string)$id_asignatura,
            'id_nivel' => (string)$oAsignatura->getId_nivel(),
            'id_activ' => (string)$id_activ,
            'f_acta' => (string)$oActa->getF_acta()?->getFromLocal(),
            'nom_activ' => (string)$nom_activ,
            'epoca' => (string)$epoca,
            'acta' => (string)$acta,
        ];
    }
}
