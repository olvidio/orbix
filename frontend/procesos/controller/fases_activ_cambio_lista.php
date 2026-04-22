<?php

use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

header('Content-Type: text/html; charset=UTF-8');

$data = PostRequest::getDataFromUrl('/src/procesos/fases_activ_cambio_lista', $_POST);

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

$oHash = new Hash();
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
