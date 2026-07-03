<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Pantalla de filtro para el balance de plazas entre dos dl:
 * muestra un desplegable con las dl disponibles y un `#comparativa`
 * vacio que se rellena via AJAX con `plazas_balance_dl.php` (frontend,
 * devuelve HTML) al cambiar el valor del select.
 *
 * Migrada desde `apps/actividadplazas/controller/plazas_balance_que.php`
 * siguiendo `refactor.md`. Datos desde `/src/actividadplazas/plazas_balance_que_data`
 * (PostRequest). Sin `use src\...`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$post = [
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
];
$dataShell = PostRequest::getDataFromUrl('/src/actividadplazas/plazas_balance_que_data', $post);
$delegacionesOpc = NotasFormSupport::desplegableOpciones($dataShell['delegaciones_opciones'] ?? []);
$Qid_tipo_activ = \frontend\shared\helpers\PayloadCoercion::string($dataShell['id_tipo_activ'] ?? '');

$desplDelegaciones = Desplegable::desdeOpciones($delegacionesOpc, 'dl');
$desplDelegaciones->setAction('fnjs_comparativa()');

$mi_dele = OrbixRuntime::miDelef();
$txt = sprintf(_("comparar %s con:"), $mi_dele);

$url_balance_dl = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividadplazas/controller/plazas_balance_dl.php';
$oHash = new HashFront();
$oHash->setUrl($url_balance_dl);
$oHash->setCamposForm('dl!id_tipo_activ');
$h = $oHash->linkSinValParams();

$a_campos = [
    'Qid_tipo_activ' => $Qid_tipo_activ,
    'h' => $h,
    'txt' => $txt,
    'desplDelegaciones' => $desplDelegaciones,
    'url_balance_dl' => $url_balance_dl,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('plazas_balance_que.phtml', $a_campos);
