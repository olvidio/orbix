<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;

/**
 * AJAX (`tipo_activ.php`): formulario para cambiar el nombre de un tipo de actividad o eliminarlo. Los textos y las
 * cápsulas `ctx_guardar` / `ctx_eliminar` vienen de `/src/actividades/tipo_activ_form_modificar_data`.
 */
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/actividades/tipo_activ_form_modificar_data', [
    'id_tipo_activ' => (int) filter_input(INPUT_POST, 'id_tipo_activ'),
]);

$oHash = new HashF();
$oHash->setCamposForm('nom_tipo_activ');
$oHash->setArrayCamposHidden([
    'id_tipo_activ' => (int) ($data['id_tipo_activ'] ?? 0),
    'ctx_guardar' => (string) ($data['ctx_guardar'] ?? ''),
    'ctx_eliminar' => (string) ($data['ctx_eliminar'] ?? ''),
]);

AjaxJsonSupport::renderPhtml('frontend\\actividades\\controller', 'tipo_activ_form_modificar.phtml', [
    'oHash' => $oHash,
    'nom_actividad' => (string) ($data['nom_actividad'] ?? ''),
    'nom_tipo' => (string) ($data['nom_tipo'] ?? ''),
]);
