<?php

use frontend\misas\support\PeriodoTdHelper;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/misas/buscar_plan_sacd_data');

$a_sacd = $data['sacd_opciones'] ?? [];
$sacd_selected = (string)($data['sacd_selected'] ?? '');

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
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(false);
$oDesplSacd->setAction('fnjs_ver_plan_sacd()');
if ($sacd_selected !== '') {
    $oDesplSacd->setOpcion_sel($sacd_selected);
}

$url_ver_plan_sacd = 'frontend/misas/controller/ver_plan_sacd.php';
$oHashPlanSacd = new Hash();
$oHashPlanSacd->setUrl($url_ver_plan_sacd);
$oHashPlanSacd->setCamposForm('id_sacd!periodo!empiezamin!empiezamax');
$h_plan_sacd = $oHashPlanSacd->linkSinValParams();

$a_campos = [
    'oDesplSacd' => $oDesplSacd,
    'periodo_td_html' => $periodo_td_html,
    'url_ver_plan_sacd' => $url_ver_plan_sacd,
    'h_plan_sacd' => $h_plan_sacd,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('buscar_plan_sacd.phtml', $a_campos);
