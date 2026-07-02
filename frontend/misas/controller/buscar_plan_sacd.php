<?php

use frontend\misas\support\PeriodoTdHelper;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
require_once 'frontend/misas/helpers/misas_support.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/misas/buscar_plan_sacd_data');

$a_sacd = $data['sacd_opciones'] ?? [];
$sacd_selected = misas_string($data['sacd_selected'] ?? '');

$periodo_td_html = PeriodoTdHelper::build([
    'esta_semana' => _('esta semana'),
    'este_mes' => _('este mes'),
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'separador' => '---------',
    'otro' => _('otro'),
], 'esta_semana');

$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones(misas_desplegable_opciones($a_sacd));
$oDesplSacd->setBlanco(false);
$oDesplSacd->setAction('fnjs_ver_plan_sacd()');
if ($sacd_selected !== '') {
    $oDesplSacd->setOpcion_sel($sacd_selected);
}
$msg = '';
if (empty($a_sacd)) {
    $msg = _('No hay SACD disponibles porque: Usuario sin csv_id_pau, que no es jefe de zona, ni p-sacd, ni Oficial_dl, ni jefe de calendario');
}

$url_ver_plan_sacd = 'frontend/misas/controller/ver_plan_sacd.php';
$oHashPlanSacd = new HashFront();
$oHashPlanSacd->setUrl($url_ver_plan_sacd);
$oHashPlanSacd->setCamposForm('id_sacd!periodo!empiezamin!empiezamax');
$h_plan_sacd = $oHashPlanSacd->linkSinValParams();

$a_campos = [
    'oDesplSacd' => $oDesplSacd,
    'periodo_td_html' => $periodo_td_html,
    'url_ver_plan_sacd' => $url_ver_plan_sacd,
    'h_plan_sacd' => $h_plan_sacd,
    'msg' => $msg,
];

ajax_json_render_phtml('frontend\\misas\\controller', 'buscar_plan_sacd.phtml', $a_campos);
