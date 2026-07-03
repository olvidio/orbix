<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\procesos\helpers\ProcesosPayload;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$requestPayload = PostRequest::requestPayloadForHash();

$oPosicion->setParametros(ProcesosPayload::fasesActivCambioGoback($requestPayload), 0);

$data = PostRequest::getDataFromUrl('/src/procesos/fases_activ_cambio_lista', $requestPayload);

$error = \frontend\shared\helpers\PayloadCoercion::string($data['error'] ?? '');
if ($error !== '') {
    AjaxJsonSupport::html('<h2>' . $error . '</h2>', $error);
}

$msg = \frontend\shared\helpers\PayloadCoercion::string($data['msg'] ?? '');
$accion = \frontend\shared\helpers\PayloadCoercion::string($data['accion'] ?? '');
$id_fase_nueva = \frontend\shared\helpers\PayloadCoercion::string($data['id_fase_nueva'] ?? '');
$a_cabeceras = ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? null);
$a_valores = ActividadesListaSupport::datos($data['a_valores'] ?? null);

$txt_cambiar = $accion === 'desmarcar' ? _("descambiar los marcados") : _("cambiar los marcados");
$a_botones = [
    ['txt' => $txt_cambiar, 'click' => "fnjs_cambiar(\"#seleccionados\")"],
    ['txt' => _("ver proceso actividad"), 'click' => "fnjs_ver_activ(\"#seleccionados\")"],
    ['txt' => _("todos"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\",0)"],
    ['txt' => _("ninguno"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\",0)"],
    ['txt' => _("invertir"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\",0)"],
];

$oTabla = new Lista();
$oTabla->setId_tabla('actividades_fases_cambio_ajax');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
$oTabla->setFormatoTabla('html');

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden([
    'id_fase_nueva' => $id_fase_nueva,
    'accion' => $accion,
]);

$html = $msg;
$html .= '<form id="seleccionados" name="seleccionados" action="" method="post">';
$html .= $oHash->getCamposHtml();
$html .= $oTabla->mostrar_tabla();
$html .= '</form>';
AjaxJsonSupport::html($html);
