<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$data = PostRequest::getDataFromUrl('/src/actividadcargos/form_cargos_personas_en_actividad_data', PostRequest::requestPayloadForHash());
if (!empty($data['error'])) {
    exit($data['error']);
}
unset($data['error']);

$data['oPosicion'] = $oPosicion;

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_personas_en_actividad.phtml', $data);
