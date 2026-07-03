<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_get2_data', [
    'id_ubi' => $Qid_ubi,
    'year' => $Qyear,
]));
$fields = UbisPayload::calendarioPeriodoFields($data);
$lista = UbisPayload::listaFromPayload($data);

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
AjaxJsonSupport::html($html);
