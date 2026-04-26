<?php

use frontend\misas\support\PeriodoTdHelper;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$data = PostRequest::getDataFromUrl('/src/misas/cambiar_status_data');

$periodo_td_html = PeriodoTdHelper::build([
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'otro' => _('otro'),
], 'proxima_semana');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($data['zonas_opciones'] ?? []);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$oDesplEstados = new Desplegable();
$oDesplEstados->setOpciones($data['estados_opciones'] ?? []);
$oDesplEstados->setNombre('estado');
$oDesplEstados->setAction('fnjs_ver_cuadricula_zona()');

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($data['orden_opciones'] ?? []);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_nuevo_status = '/src/misas/nuevo_status';
$oHashNuevoStatus = new Hash();
$oHashNuevoStatus->setUrl($url_nuevo_status);
$oHashNuevoStatus->setCamposForm('id_zona!periodo!estado!empiezamin!empiezamax');
$h_nuevo_status = $oHashNuevoStatus->linkSinValParams();

$url_ver_cuadricula_zona = 'frontend/misas/controller/ver_cuadricula_zona.php';
$oHashZonaStatus = new Hash();
$oHashZonaStatus->setUrl($url_ver_cuadricula_zona);
$oHashZonaStatus->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_status = $oHashZonaStatus->linkSinValParams();

$oHash = new Hash();
$oHash->setUrl('frontend/misas/controller/cambiar_status.php');
$oHash->setCamposForm('id_zona!estado!orden!periodo!empiezamin!empiezamax');

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplEstados' => $oDesplEstados,
    'oDesplOrden' => $oDesplOrden,
    'periodo_td_html' => $periodo_td_html,
    'url_nuevo_status' => $url_nuevo_status,
    'h_nuevo_status' => $h_nuevo_status,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_status' => $h_zona_status,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('cambiar_status.phtml', $a_campos);
