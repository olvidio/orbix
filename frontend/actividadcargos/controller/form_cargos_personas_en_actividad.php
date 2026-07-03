<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose;
use frontend\actividadcargos\helpers\ActividadcargosPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

\frontend\shared\helpers\ListNavSupport::bootDossierChildRecordar($oPosicion);


$raw = PostRequest::getDataFromUrl('/src/actividadcargos/form_cargos_personas_en_actividad_data', PostRequest::requestPayloadForHash());
if (!empty($raw['error'])) {
    exit($raw['error']);
}
unset($raw['error']);

$data = FormCargosDeActividadHashCompose::withDesplegablesHtml(
    FormCargosDeActividadHashCompose::withHashCamposHtml(ActividadcargosPayload::stringKeyPayload($raw))
);

$data['oPosicion'] = $oPosicion;

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_personas_en_actividad.phtml', $data);
