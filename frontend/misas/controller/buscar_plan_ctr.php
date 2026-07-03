<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\misas\support\PeriodoTdHelper;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\misas\helpers\MisasDesplegableSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$id_zona = (int)filter_input(INPUT_POST, 'id_zona');

$data = PostRequest::getDataFromUrl('/src/misas/buscar_plan_ctr_data', ['id_zona' => $id_zona]);

$view = $data['view'] ?? 'none';

$periodo_td_html = PeriodoTdHelper::build([
    'esta_semana' => _('esta semana'),
    'este_mes' => _('este mes'),
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'separador' => '---------',
    'otro' => _('otro'),
], 'esta_semana');

$oDesplZonas = new Desplegable();
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_buscar_plan_ctr()');
$oDesplZonas->setOpciones(MisasDesplegableSupport::opciones($data['zonas_opciones'] ?? []));
$oDesplZonas->setOpcion_sel(PayloadCoercion::string($data['zonas_selected'] ?? 0));

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones(MisasDesplegableSupport::opciones($data['centros_opciones'] ?? []));
$oDesplCentros->setAction('fnjs_ver_plan_ctr()');
$cs = PayloadCoercion::string($data['centros_selected'] ?? '');
if ($cs !== '') {
    $oDesplCentros->setOpcion_sel($cs);
}

$url_buscar_plan_ctr = 'frontend/misas/controller/buscar_plan_ctr.php';
$oHashBuscarPlanCtr = new HashFront();
$oHashBuscarPlanCtr->setUrl($url_buscar_plan_ctr);
$oHashBuscarPlanCtr->setCamposForm('id_zona');
$h_buscar_plan_ctr = $oHashBuscarPlanCtr->linkSinValParams();

$url_ver_plan_ctr = 'frontend/misas/controller/ver_plan_ctr.php';
$oHashPlanCtr = new HashFront();
$oHashPlanCtr->setUrl($url_ver_plan_ctr);
$oHashPlanCtr->setCamposForm('id_zona!id_ubi!periodo!empiezamin!empiezamax');
$h_plan_ctr = $oHashPlanCtr->linkSinValParams();

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplCentros' => $oDesplCentros,
    'periodo_td_html' => $periodo_td_html,
    'url_buscar_plan_ctr' => $url_buscar_plan_ctr,
    'url_ver_plan_ctr' => $url_ver_plan_ctr,
    'h_buscar_plan_ctr' => $h_buscar_plan_ctr,
    'h_plan_ctr' => $h_plan_ctr,
    'id_ubi_centro' => PayloadCoercion::string($data['id_ubi_centro'] ?? ''),
];

$template = $view === 'centro' ? 'buscar_plan_un_ctr.phtml' : 'buscar_plan_ctr.phtml';
AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', $template, $a_campos);
