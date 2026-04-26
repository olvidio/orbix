<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;
use frontend\shared\web\Lista;

/**
 * Listado de horarios de un encargo. Los datos vienen de
 * {@see \src\encargossacd\application\EncargoHorarioSelectData} via
 * `/src/encargossacd/encargo_horario_select_data`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($Qid_sel)) {
    $Qid_enc = (int)strtok((string)$Qid_sel[0], '#');
} else {
    $Qid_enc = (int)filter_input(INPUT_POST, 'id_enc');
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qorigen = (string)filter_input(INPUT_POST, 'origen');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/encargo_horario_select_data', [
    'id_enc' => $Qid_enc,
]);
$desc_enc = (string)($data['desc_enc'] ?? '');
$filas = is_array($data['filas'] ?? null) ? $data['filas'] : [];

$titulo = $desc_enc;

$a_botones = [
    ['txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"],
    ['txt' => _("eliminar"), 'click' => "fnjs_borrar(\"#seleccionados\")"],
];

$a_cabeceras = [
    ['name' => ucfirst(_("id")), 'formatter' => 'clickFormatter'],
    _("ord."),
    _("dia ref"),
    _("signo"),
    _("variación"),
    _("hora ini"),
    _("hora fin"),
    _("nº sacd"),
    _("mes"),
    ['name' => ucfirst(_("f ini")), 'class' => 'fecha'],
    ['name' => ucfirst(_("f fin")), 'class' => 'fecha'],
    _("excepciones"),
    _("texto"),
];

$a_valores = [];
if (!empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
$i = 0;
foreach ($filas as $fila) {
    $i++;
    $id_enc_fila = (int)($fila['id_enc'] ?? 0);
    $id_item_h = (int)($fila['id_item_h'] ?? 0);

    $aQuery = [
        'mod' => 'editar',
        'id_enc' => $id_enc_fila,
        'id_item_h' => $id_item_h,
        'desc_enc' => $desc_enc,
    ];
    array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
    $pagina = Hash::link('frontend/encargossacd/controller/horario_ver.php?' . http_build_query($aQuery));

    $a_valores[$i]['sel'] = $id_item_h;
    $a_valores[$i][1] = ['ira' => $pagina, 'valor' => $id_enc_fila];
    $a_valores[$i][3] = (string)($fila['dia_num'] ?? '');
    $a_valores[$i][4] = (string)($fila['dia_ref'] ?? '');
    $a_valores[$i][5] = (string)($fila['mas_menos'] ?? '');
    $a_valores[$i][6] = (string)($fila['dia_inc'] ?? '');
    $a_valores[$i][7] = (string)($fila['h_ini'] ?? '');
    $a_valores[$i][8] = (string)($fila['h_fin'] ?? '');
    $a_valores[$i][9] = (string)($fila['n_sacd'] ?? '');
    $a_valores[$i][10] = (string)($fila['mes'] ?? '');
    $a_valores[$i][11] = $fila['f_ini'] ?? null;
    $a_valores[$i][12] = $fila['f_fin'] ?? null;
    $a_valores[$i][13] = (string)($fila['excep'] ?? '');
    $a_valores[$i][14] = (string)($fila['texto_horario'] ?? '');
}

$aQuery = [
    'mod' => 'nuevo',
    'id_enc' => $Qid_enc,
    'desc_enc' => $desc_enc,
    'origen' => $Qorigen,
];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$pagina_nuevo = Hash::link('frontend/encargossacd/controller/horario_ver.php?' . http_build_query($aQuery));

$oTabla = new Lista();
$oTabla->setId_tabla('encargo_horario_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$url_actualizar = 'frontend/encargossacd/controller/horario_ver.php';
$oHash = new Hash();
$oHash->setUrl($url_actualizar);
$oHash->setArrayCamposHidden([
    'mod' => $Qmod,
    'desc_enc' => $desc_enc,
]);

$txt_eliminar = _("¿Está seguro que desea borrar este horario?");

$div_para_nuevo = 'main';
if ($Qorigen === 'misas') {
    $div_para_nuevo = 'div_modificar5';
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'txt_eliminar' => $txt_eliminar,
    'pagina_nuevo' => $pagina_nuevo,
    'desc_enc' => $desc_enc,
    'origen' => $Qorigen,
    'div_para_nuevo' => $div_para_nuevo,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('encargo_horario_select.phtml', $a_campos);
