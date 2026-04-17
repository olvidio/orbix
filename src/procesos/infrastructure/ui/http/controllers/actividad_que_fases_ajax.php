<?php

/*
 * Port de apps/procesos/controller/actividad_que_fases_ajax.php.
 * Devuelve HTML (text/plain) con los checkboxes de fases para
 * `fases_on` / `fases_off` en funcion de los tipos de proceso de una
 * actividad. Consumido desde `frontend/actividades/view/actividad_que`.
 */

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use function core\is_true;

header('Content-Type: text/plain; charset=UTF-8');

$Qsalida = (string)filter_input(INPUT_POST, 'salida');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$dl_propia = is_true($Qdl_propia);

$TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
$aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $dl_propia);

$ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
$aFases = $ActividadFaseRepository->getArrayFasesProcesos($aTiposDeProcesos);

switch ($Qsalida) {
    case 'fases_on':
        $html = '';
        foreach ($aFases as $descripcion => $id_fase) {
            $html .= "<input type='checkbox' name='fases_on[]' value='$id_fase' /> $descripcion";
        }
        echo $html;
        break;
    case 'fases_off':
        $html = '';
        foreach ($aFases as $descripcion => $id_fase) {
            $html .= "<input type='checkbox' name='fases_off[]' value='$id_fase' /> $descripcion";
        }
        echo $html;
        break;
}
