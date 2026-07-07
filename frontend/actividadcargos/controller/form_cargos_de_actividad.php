<?php

use frontend\actividadcargos\helpers\ActividadcargosPayload;
use frontend\shared\helpers\ListNavSupport;

/**
 * @param string  $_POST['pau']
 * @param integer $_POST['id_pau']
 * @param string  $_POST['obj_pau']
 * @param integer $_POST['id_dossier']  3102 | 3101
 * @param string  $_POST['mod']
 * @param integer $_POST['permiso']
 * @param array   $_POST['sel']
 */

use frontend\shared\FrontBootstrap;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
ListNavSupport::enterDossierChildNav($oPosicion);

$raw = PostRequest::getDataFromUrl('/src/actividadcargos/form_cargos_de_actividad_data', PostRequest::requestPayloadForHash());
if (!empty($raw['error'])) {
    exit($raw['error']);
}
if (($raw['redir'] ?? '') === 'go_atras') {
    echo '<script>' . $oPosicion->jsNavAtrasToDossiersParent() . '</script>';
    return;
}
unset($raw['redir'], $raw['error']);

$data = FormCargosDeActividadHashCompose::withHashCamposHtml(ActividadcargosPayload::stringKeyPayload($raw));
$data = FormCargosDeActividadHashCompose::withDesplegablesHtml($data);

$data['oPosicion'] = $oPosicion;

(new ViewNewPhtml('frontend\\actividadcargos\\controller'))
    ->renderizar('form_cargos_de_actividad.phtml', $data);
