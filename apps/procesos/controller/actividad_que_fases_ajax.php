<?php

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qsalida = (string)filter_input(INPUT_POST, 'salida');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
if (is_true($Qdl_propia)) {
    $dl_propia = TRUE;
} else {
    $dl_propia = FALSE;
}


// buscar los procesos posibles para estos tipos de actividad
$TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
$aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $dl_propia);
$ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
$aFases = $ActividadFaseRepository->getArrayFasesProcesos($aTiposDeProcesos);

// buscar las fases para estos procesos
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