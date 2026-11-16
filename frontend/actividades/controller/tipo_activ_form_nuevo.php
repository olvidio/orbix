<?php

use frontend\actividades\helpers\ActividadTipo;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;

/**
 * AJAX (`tipo_activ.php`): formulario de alta de un tipo de actividad. La cápsula `ctx_nuevo` la firma
 * `/src/actividades/tipo_activ_form_nuevo_data`.
 */
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/actividades/tipo_activ_form_nuevo_data', []);

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setPerm_jefe(true);
$oActividadTipo->setEvitarProcesos(true);
$oActividadTipo->setPara('gestion');

$oHash = new HashF();
$oHash->setCamposForm('iactividad_val!iasistentes_val!id_nom_tipo_activ!isfsv_val!nom_tipo_activ');
// Los desplegables del tipo añaden su oculto `extendida`: sin esto el alta se rechazaba (302 al inicio).
$oHash->setCamposNo('extendida');
$oHash->setArrayCamposHidden(['ctx_nuevo' => (string) ($data['ctx_nuevo'] ?? '')]);

AjaxJsonSupport::renderPhtml('frontend\\actividades\\controller', 'tipo_activ_form_nuevo.phtml', [
    'oHash' => $oHash,
    'html_tipo' => $oActividadTipo->captureHtml(true),
]);
