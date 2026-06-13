<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

/**
 * Listado de horarios de un encargo. Los datos vienen de
 * {@see \src\encargossacd\application\EncargoHorarioSelectData} via
 * `/src/encargossacd/encargo_horario_select_data`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($Qid_sel)) {
    $Qid_enc = (int)strtok((string)$Qid_sel[0], '#');
} else {
    $Qid_enc = encargossacd_post_int('id_enc');
}

$Qmod = encargossacd_post_string('mod');
$Qorigen = encargossacd_post_string('origen');

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/encargo_horario_select_data', [
    'id_enc' => $Qid_enc,
]);
$desc_enc = tessera_imprimir_string($data['desc_enc'] ?? '');
$filasRaw = $data['filas'] ?? [];
$filas = is_array($filasRaw) ? $filasRaw : [];

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
    $row = encargossacd_horario_row($fila);
    $i++;
    $id_enc_fila = $row['id_enc'];
    $id_item_h = $row['id_item_h'];

    $aQuery = [
        'mod' => 'editar',
        'id_enc' => $id_enc_fila,
        'id_item_h' => $id_item_h,
        'desc_enc' => $desc_enc,
    ];
    array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
    $pagina = HashFront::link('frontend/encargossacd/controller/horario_ver.php?' . http_build_query($aQuery));

    $a_valores[$i] = [
        'sel' => $id_item_h,
        1 => ['ira' => $pagina, 'valor' => $id_enc_fila],
        3 => $row['dia_num'],
        4 => $row['dia_ref'],
        5 => $row['mas_menos'],
        6 => $row['dia_inc'],
        7 => $row['h_ini'],
        8 => $row['h_fin'],
        9 => $row['n_sacd'],
        10 => $row['mes'],
        11 => $row['f_ini'],
        12 => $row['f_fin'],
        13 => $row['excep'],
        14 => $row['texto_horario'],
    ];
}

$aQuery = [
    'mod' => 'nuevo',
    'id_enc' => $Qid_enc,
    'desc_enc' => $desc_enc,
    'origen' => $Qorigen,
];
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$pagina_nuevo = HashFront::link('frontend/encargossacd/controller/horario_ver.php?' . http_build_query($aQuery));

$oTabla = new Lista();
$oTabla->setId_tabla('encargo_horario_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$url_actualizar = 'frontend/encargossacd/controller/horario_ver.php';
$oHash = new HashFront();
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
