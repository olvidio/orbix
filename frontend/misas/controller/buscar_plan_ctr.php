<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$id_zona = (int)filter_input(INPUT_POST, 'id_zona');

$data = PostRequest::getDataFromUrl('/src/misas/buscar_plan_ctr_data', ['id_zona' => $id_zona]);

$view = $data['view'] ?? 'none';
$periodo_td_html = (string)($data['periodo_td_html'] ?? '');

$oDesplZonas = new Desplegable();
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_buscar_plan_ctr()');
$oDesplZonas->setOpciones($data['zonas_opciones'] ?? []);
$oDesplZonas->setOpcion_sel((string)($data['zonas_selected'] ?? 0));

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($data['centros_opciones'] ?? []);
$oDesplCentros->setAction('fnjs_ver_plan_ctr()');
$cs = (string)($data['centros_selected'] ?? '');
if ($cs !== '') {
    $oDesplCentros->setOpcion_sel($cs);
}

$url_buscar_plan_ctr = 'frontend/misas/controller/buscar_plan_ctr.php';
$oHashBuscarPlanCtr = new Hash();
$oHashBuscarPlanCtr->setUrl($url_buscar_plan_ctr);
$oHashBuscarPlanCtr->setCamposForm('id_zona');
$h_buscar_plan_ctr = $oHashBuscarPlanCtr->linkSinVal();

$url_ver_plan_ctr = 'frontend/misas/controller/ver_plan_ctr.php';
$oHashPlanCtr = new Hash();
$oHashPlanCtr->setUrl($url_ver_plan_ctr);
$oHashPlanCtr->setCamposForm('id_zona!id_ubi!periodo!empiezamin!empiezamax');
$h_plan_ctr = $oHashPlanCtr->linkSinVal();

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplCentros' => $oDesplCentros,
    'periodo_td_html' => $periodo_td_html,
    'url_buscar_plan_ctr' => $url_buscar_plan_ctr,
    'url_ver_plan_ctr' => $url_ver_plan_ctr,
    'h_buscar_plan_ctr' => $h_buscar_plan_ctr,
    'h_plan_ctr' => $h_plan_ctr,
    'id_ubi_centro' => (string)($data['id_ubi_centro'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
if ($view === 'centro') {
    $oView->renderizar('buscar_plan_un_ctr.phtml', $a_campos);
} else {
    $oView->renderizar('buscar_plan_ctr.phtml', $a_campos);
}
