<?php
/**
 * Pantalla que dispara la incorporacion de las primeras peticiones
 * como asistencia (accion contra `/src/actividadplazas/peticiones_incorporar`).
 * Muestra mensaje final con numero de incorporaciones y posibles
 * errores.
 *
 * Migrada desde `apps/actividadplazas/controller/incorporar_peticion.php`
 * (que mezclaba UI + mutacion) siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');

$ctxData = PostRequest::getDataFromUrl('/src/actividadplazas/peticiones_incorporar_data', []);
$ctx_incorporar = PayloadCoercion::string($ctxData['ctx_incorporar'] ?? '');

$oHash = new HashF();
$oHash->setUrl(AppUrlConfig::srcBrowserUrl('/src/actividadplazas/peticiones_incorporar'));
$oHash->setCamposForm('sactividad!sasistentes!ctx_incorporar');
$url_incorporar = AppUrlConfig::srcBrowserUrl('/src/actividadplazas/peticiones_incorporar') . $oHash->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'sactividad' => $Qsactividad,
    'sasistentes' => $Qsasistentes,
    'url_incorporar' => $url_incorporar,
    'ctx_incorporar' => $ctx_incorporar,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('incorporar_peticion.phtml', $a_campos);
