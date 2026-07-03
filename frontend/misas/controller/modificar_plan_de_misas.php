<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\misas\support\PeriodoTdHelper;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\misas\helpers\MisasDesplegableSupport;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/misas/plan_de_misas_pantalla_data', ['pantalla' => 'modificar']);

$periodo_td_html = PeriodoTdHelper::build([
    'esta_semana' => _('esta semana'),
    'este_mes' => _('este mes'),
    'proxima_semana' => _('próxima semana de lunes a domingo'),
    'proximo_mes' => _('próximo mes natural'),
    'separador' => '---------',
    'otro' => _('otro'),
], 'proximo_mes');

$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones(MisasDesplegableSupport::opciones($data['zonas_opciones'] ?? []));
$oDesplZonas->setBlanco(false);
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_modificar_cuadricula_zona()');

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones(MisasDesplegableSupport::opciones($data['orden_opciones'] ?? []));
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_modificar_cuadricula_zona()');

$url_modificar_cuadricula_zona = 'frontend/misas/controller/modificar_cuadricula_zona.php';
$oHashZonaPeriodo = new HashFront();
$oHashZonaPeriodo->setUrl($url_modificar_cuadricula_zona);
$oHashZonaPeriodo->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_periodo = $oHashZonaPeriodo->linkSinValParams();

$oHash = new HashFront();
$oHash->setUrl('frontend/misas/controller/modificar_plan_de_misas.php');
$oHash->setCamposForm('id_zona!orden!periodo!empiezamin!empiezamax');

$a_campos = [
    'oDesplZonas' => $oDesplZonas,
    'oDesplOrden' => $oDesplOrden,
    'periodo_td_html' => $periodo_td_html,
    'url_modificar_cuadricula_zona' => $url_modificar_cuadricula_zona,
    'h_zona_periodo' => $h_zona_periodo,
    'oHash' => $oHash,
];

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'modificar_plan_de_misas.phtml', $a_campos);
