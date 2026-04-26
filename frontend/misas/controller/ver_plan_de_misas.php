<?php

use frontend\misas\support\PeriodoTdHelper;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/misas/plan_de_misas_pantalla_data', ['pantalla' => 'ver']);

$periodo_td_html = PeriodoTdHelper::build([
    'esta_semana' => _('esta semana'),
    'este_mes' => _('este mes'),
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'separador' => '---------',
    'otro' => _('otro'),
], 'este_mes');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($data['zonas_opciones'] ?? []);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($data['orden_opciones'] ?? []);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_ver_cuadricula_zona = 'frontend/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new Hash();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_periodo = $oHashZonaPeriodo->linkSinValParams();

$oHash = new Hash();
$oHash->setUrl('frontend/misas/controller/ver_plan_de_misas.php');
$oHash->setCamposForm('id_zona!orden!periodo!empiezamin!empiezamax');

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplOrden' => $oDesplOrden,
    'periodo_td_html' => $periodo_td_html,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_plan_de_misas.phtml', $a_campos);
