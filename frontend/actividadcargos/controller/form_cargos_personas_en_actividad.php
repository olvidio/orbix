<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();

$data = PostRequest::getDataFromUrl('/src/actividadcargos/form_cargos_personas_en_actividad_data', PostRequest::requestPayloadForHash());
if (!empty($data['error'])) {
    exit($data['error']);
}
unset($data['error']);

$data = FormCargosDeActividadHashCompose::withHashCamposHtml($data);

$data['oPosicion'] = $oPosicion;

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_personas_en_actividad.phtml', $data);
