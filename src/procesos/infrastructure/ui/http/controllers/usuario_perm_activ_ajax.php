<?php

/*
 * Endpoint para refrescar las fases disponibles del desplegable
 * `fase_ref` en la pantalla usuario_perm_activ segun el tipo de actividad
 * seleccionado y si se trata de la delegacion propia o no.
 *
 * Port 1:1 del controlador legacy apps/procesos/controller/usuario_perm_activ_ajax.php.
 */

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;

header('Content-Type: text/plain; charset=UTF-8');

$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

$TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
$aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, $Qdl_propia);
$ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
$aOpciones = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);

echo $aOpciones;
