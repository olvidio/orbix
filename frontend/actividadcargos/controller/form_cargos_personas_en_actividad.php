<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once 'frontend/actividadcargos/helpers/actividadcargos_support.php';

$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$raw = PostRequest::getDataFromUrl('/src/actividadcargos/form_cargos_personas_en_actividad_data', PostRequest::requestPayloadForHash());
if (!empty($raw['error'])) {
    exit($raw['error']);
}
unset($raw['error']);

$data = FormCargosDeActividadHashCompose::withHashCamposHtml(actividadcargos_string_key_payload($raw));

$data['oPosicion'] = $oPosicion;

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_personas_en_actividad.phtml', $data);
