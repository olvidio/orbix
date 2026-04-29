<?php

/**
 * @param string  $_POST['pau']
 * @param integer $_POST['id_pau']
 * @param string  $_POST['obj_pau']
 * @param integer $_POST['id_dossier']  3102 | 3101
 * @param string  $_POST['mod']
 * @param integer $_POST['permiso']
 * @param array   $_POST['sel']
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$data = PostRequest::getDataFromUrl('/src/actividadcargos/form_cargos_de_actividad_data', PostRequest::requestPayloadForHash());
if (!empty($data['error'])) {
    exit($data['error']);
}
if (($data['redir'] ?? '') === 'go_atras') {
    echo $oPosicion->go_atras(1);
    return;
}
unset($data['redir'], $data['error']);

$data = FormCargosDeActividadHashCompose::withHashCamposHtml($data);
$data = FormCargosDeActividadHashCompose::withDesplegablesHtml($data);

$data['oPosicion'] = $oPosicion;

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_de_actividad.phtml', $data);
