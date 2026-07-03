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
$data = PostRequest::getDataFromUrl('/src/misas/plan_de_misas_pantalla_data', ['pantalla' => 'preparar']);

$periodo_td_html = PeriodoTdHelper::build([
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'otro' => _('otro'),
], 'proxima_semana');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones(MisasDesplegableSupport::opciones($data['zonas_opciones'] ?? []));
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$oDesplTipoPlantilla = new Desplegable();
$oDesplTipoPlantilla->setOpciones(MisasDesplegableSupport::opciones($data['tipos_plantilla'] ?? []));
$oDesplTipoPlantilla->setNombre('tipoplantilla');
$oDesplTipoPlantilla->setOpcion_sel(PayloadCoercion::string($data['plantilla_selected'] ?? ''));
$oDesplTipoPlantilla->setAction('fnjs_ver_cuadricula_zona()');

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones(MisasDesplegableSupport::opciones($data['orden_opciones'] ?? []));
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_crear_nuevo_periodo = 'frontend/misas/controller/crear_nuevo_periodo.php';
$oHashNuevoPeriodo = new HashFront();
$oHashNuevoPeriodo->setUrl($url_crear_nuevo_periodo);
$oHashNuevoPeriodo->setCamposForm('id_zona!tipoplantilla!periodo!empiezamin!empiezamax');
$h_nuevo_periodo = $oHashNuevoPeriodo->linkSinValParams();

$url_ver_cuadricula_zona = 'frontend/misas/controller/ver_cuadricula_zona.php';
$oHashZonaPeriodo = new HashFront();
$oHashZonaPeriodo->setUrl($url_ver_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_cuadricula_zona = $oHashZonaPeriodo->linkSinValParams();

$oHash = new HashFront();
$oHash->setUrl('frontend/misas/controller/preparar_plan_de_misas.php');
$oHash->setCamposForm('id_zona!tipoplantilla!orden!periodo!empiezamin!empiezamax');

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplTipoPlantilla' => $oDesplTipoPlantilla,
    'oDesplOrden' => $oDesplOrden,
    'periodo_td_html' => $periodo_td_html,
    'url_crear_nuevo_periodo' => $url_crear_nuevo_periodo,
    'h_nuevo_periodo' => $h_nuevo_periodo,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_cuadricula_zona' => $h_cuadricula_zona,
    'oHash' => $oHash,
];

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'preparar_plan_de_misas.phtml', $a_campos);
