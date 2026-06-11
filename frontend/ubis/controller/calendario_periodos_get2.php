<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_get2_data', [
    'id_ubi' => $Qid_ubi,
    'year' => $Qyear,
]));
$fields = ubis_calendario_periodo_fields($data);
$lista = ubis_lista_from_payload($data);

$oLista = new Lista();
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
$html = $oLista->lista();
if ($fields['overlap_error'] !== '') {
    $html .= "<span class='alert'>{$fields['overlap_error']}</span>";
}
if ($fields['show_nuevo']) {
    $html .= "<br><input type='button' value='" . _('nuevo') . "' onclick=\"fnjs_modificar();\" >";
}
ajax_json_html($html);
