<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

require_once("frontend/shared/global_header_front.inc");

header('Content-Type: text/html; charset=UTF-8');

$requestPayload = PostRequest::requestPayloadForHash();

// Estado de navegación: el frame de la página padre (fases_activ_cambio.php) ya está en la pila
// (lo añadió su propio controller vía $oPosicion->recordar()). Aquí sólo persistimos los filtros
// actuales en ese frame para que al volver se muestre la misma vista filtrada.
$aGoBack = [
    'refresh' => 1,
    'hnov' => 0,
    'dl_propia' => (string)($requestPayload['dl_propia'] ?? ''),
    'id_fase_nueva' => (string)($requestPayload['id_fase_nueva'] ?? ''),
    'id_tipo_activ' => (string)($requestPayload['id_tipo_activ'] ?? ''),
    'periodo' => (string)($requestPayload['periodo'] ?? ''),
    'year' => (string)($requestPayload['year'] ?? ''),
    'empiezamin' => (string)($requestPayload['empiezamin'] ?? ''),
    'empiezamax' => (string)($requestPayload['empiezamax'] ?? ''),
    'accion' => (string)($requestPayload['accion'] ?? ''),
];
$oPosicion->setParametros($aGoBack, 0);

$data = PostRequest::getDataFromUrl('/src/procesos/fases_activ_cambio_lista', $requestPayload);

$error = (string)($data['error'] ?? '');
if ($error !== '') {
    echo '<h2>' . $error . '</h2>';
    return;
}

$msg = (string)($data['msg'] ?? '');
$accion = (string)($data['accion'] ?? '');
$id_fase_nueva = (string)($data['id_fase_nueva'] ?? '');
$a_cabeceras = (array)($data['a_cabeceras'] ?? []);
$a_valores = (array)($data['a_valores'] ?? []);

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

echo $msg;
echo '<form id="seleccionados" name="seleccionados" action="" method="post">';
echo $oHash->getCamposHtml();
echo $oTabla->mostrar_tabla();
echo '</form>';
