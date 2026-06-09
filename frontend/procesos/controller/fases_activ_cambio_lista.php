<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/procesos_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
header('Content-Type: text/html; charset=UTF-8');

$requestPayload = PostRequest::requestPayloadForHash();

$oPosicion->setParametros(procesos_fases_activ_cambio_goback($requestPayload), 0);

$data = PostRequest::getDataFromUrl('/src/procesos/fases_activ_cambio_lista', $requestPayload);

$error = tessera_imprimir_string($data['error'] ?? '');
if ($error !== '') {
    echo '<h2>' . $error . '</h2>';
    return;
}

$msg = tessera_imprimir_string($data['msg'] ?? '');
$accion = tessera_imprimir_string($data['accion'] ?? '');
$id_fase_nueva = tessera_imprimir_string($data['id_fase_nueva'] ?? '');
$a_cabeceras = actividades_lista_cabeceras($data['a_cabeceras'] ?? null);
$a_valores = actividades_lista_datos($data['a_valores'] ?? null);

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
