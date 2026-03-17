<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\infrastructure\ui\http\controllers\ListaHabitacionesAjax;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

// Recibe por POST un array tipo: sel[] ="300123715#crt n  Castelldaura Mas (26/3/2026-1/4/2026)-dlb"
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) {
    $Qid_activ = strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}


$url_backend = '/src/ubiscamas/HabitacionesCamaLista.php';
$a_campos_backend = ['id_activ' => $Qid_activ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);


if (isset($data['error'])) {
    exit($data['error']);
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'id_activ' => $data['id_activ'],
    'id_ubi' => $data['id_ubi'],
    'habitaciones_con_camas' => $data['habitaciones_con_camas'],
    'camas_con_asistentes' => $data['camas_con_asistentes'],
    'asistentes_sin_cama' => $data['asistentes_sin_cama'],
    'status_code' => 200,
];

// Hash para la actualización
$oHashActualizar = new Hash();
$oHashActualizar->setCamposNo('refresh');
$a_camposHiddenActualizar = [
    'id_activ' => $Qid_activ,
];
$oHashActualizar->setArraycamposHidden($a_camposHiddenActualizar);
$a_campos['oHashActualizar'] = $oHashActualizar;

// Url para grabar la asignación de cama
$url_update_cama = 'src/ubiscamas/update_cama_asistente';
$oHashUpdateCama = new Hash();
$oHashUpdateCama->setUrl($url_update_cama);
$oHashUpdateCama->setCamposForm('id_activ!id_nom!id_cama');
$a_campos['url_update_cama'] = $url_update_cama;
$a_campos['oHashUpdateCama'] = $oHashUpdateCama;

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones.phtml', $a_campos);