<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
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
}
else {
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}


$url_backend = '/src/ubiscamas/actividad_habitaciones_lista';
$a_campos_backend = ['id_activ' => $Qid_activ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);


if (isset($data['error'])) {
    exit($data['error']);
}

// tabla izquierda:
$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];

$oTabla = new Lista();
$oTabla->setId_tabla('grupo_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


$a_campos = [
    'oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'id_activ' => $data['id_activ'],
    'id_ubi' => $data['id_ubi'],
    'habitaciones_con_camas' => $data['habitaciones_con_camas'],
    'camas_con_asistentes' => $data['camas_con_asistentes'],
    'asistentes_sin_cama' => $data['asistentes_sin_cama'],
    'status_code' => 200,
];

// Hash para la actualización
$url_actualizar = 'frontend/ubiscamas/controller/lista_habitaciones.php';
$oHashActualizar = new Hash();
$oHashActualizar->setUrl($url_actualizar);
$oHashActualizar->setCamposNo('refresh');
$a_camposHiddenActualizar = [
    'id_activ' => $Qid_activ,
    'refresh' => 1,
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

// Solo VIP
$a_campos['solo_vip'] = $data['solo_vip'];
$url_update_solo_vip = '/src/ubiscamas/update_solo_vip';
$oHashSoloVip = new Hash();
$oHashSoloVip->setUrl($url_update_solo_vip);
$oHashSoloVip->setArraycamposHidden(['id_activ' => $Qid_activ]);
$oHashSoloVip->setCamposChk('solo_vip');
$a_campos['url_update_solo_vip'] = $url_update_solo_vip;
$a_campos['oHashSoloVip'] = $oHashSoloVip;

// Hash para el reporte de distribución
$url_distribucion = 'frontend/ubiscamas/controller/lista_habitaciones_distribucion.php';
$oHashDistribucion = new Hash();
$oHashDistribucion->setUrl($url_distribucion);
$oHashDistribucion->setArraycamposHidden(['id_activ' => $Qid_activ]);
$a_campos['url_distribucion'] = $url_distribucion;
$a_campos['oHashDistribucion'] = $oHashDistribucion;

// Hash para el reporte de distribución por nombres
$url_nombres = 'frontend/ubiscamas/controller/lista_habitaciones_nombres.php';
$oHashNombres = new Hash();
$oHashNombres->setUrl($url_nombres);
$oHashNombres->setArraycamposHidden(['id_activ' => $Qid_activ]);
$a_campos['url_nombres'] = $url_nombres;
$a_campos['oHashNombres'] = $oHashNombres;

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('lista_habitaciones.phtml', $a_campos);