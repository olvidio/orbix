<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use web\Hash;

/**
 * Formulario horario de encargo. Datos: `/src/encargossacd/horario_ver_data`
 * (ver {@see \src\encargossacd\application\EncargoHorarioVerData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qorigen = (string)filter_input(INPUT_POST, 'origen');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');
$Qdesc_enc = urldecode($Qdesc_enc);

$id_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
if (!empty($_POST['sel']) && is_array($_POST['sel'])) {
    $parts = explode('#', (string)$_POST['sel'][0], 2);
    $id_item_h = (int)($parts[0] ?? $id_item_h);
}

/** @var array<string, mixed> $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/horario_ver_data', [
    'mod' => $Qmod,
    'id_enc' => $Qid_enc,
    'id_item_h' => $id_item_h,
]);

$f_ini = (string)($data['f_ini'] ?? '');
$f_fin = (string)($data['f_fin'] ?? '');
$dia_ref = (string)($data['dia_ref'] ?? '');
$dia_num = (string)($data['dia_num'] ?? '');
$mas_menos = (string)($data['mas_menos'] ?? '');
$dia_inc = (string)($data['dia_inc'] ?? '');
$h_ini = (string)($data['h_ini'] ?? '');
$h_fin = (string)($data['h_fin'] ?? '');
$n_sacd = (string)($data['n_sacd'] ?? '');
$mes = (string)($data['mes'] ?? '');
$id_item_h = (string)($data['id_item_h'] ?? '');
$dia = (string)($data['dia'] ?? '');
$opciones_dia_semana = is_array($data['opciones_dia_semana'] ?? null) ? $data['opciones_dia_semana'] : [];
$opciones_dia_ref = is_array($data['opciones_dia_ref'] ?? null) ? $data['opciones_dia_ref'] : [];
$opciones_ordinales = is_array($data['opciones_ordinales'] ?? null) ? $data['opciones_ordinales'] : [];

$titulo = _("horario de") . ": " . $Qdesc_enc;

$oDesplDia = new Desplegable();
$oDesplDia->setNombre('dia');
$oDesplDia->setOpciones($opciones_dia_semana);
$oDesplDia->setOpcion_sel($dia);
$oDesplDia->setBlanco('t');

$oDesplMas = new Desplegable();
$oDesplMas->setNombre('mas_menos');
$oDesplMas->setBlanco('t');
$aOpciones = [
    "-" => _("antes del"),
    "+" => _("después del"),
];
$oDesplMas->setOpciones($aOpciones);
$oDesplMas->setOpcion_sel($mas_menos);

$oDesplOrd = new Desplegable();
$oDesplOrd->setNombre('dia_num');
$oDesplOrd->setBlanco('t');
$oDesplOrd->setOpciones($opciones_ordinales);
$oDesplOrd->setOpcion_sel($dia_num);

$oDesplRef = new Desplegable();
$oDesplRef->setNombre('dia_ref');
$oDesplRef->setBlanco('t');
$oDesplRef->setOpciones($opciones_dia_ref);
$oDesplRef->setOpcion_sel($dia_ref);

$url_actualizar = 'frontend/encargossacd/controller/encargo_ver.php';
$oHash = new Hash();
$aCamposHidden = [
    'mod' => $Qmod,
    'id_enc' => $Qid_enc,
    'id_item_h' => $id_item_h,
    'desc_enc' => $Qdesc_enc,
];

$oHash->setUrl($url_actualizar);
$campos_form = 'desc_enc!dia!dia_inc!dia_num!dia_ref!f_fin!f_ini!h_fin!h_ini!id_enc!id_item_h!mas_menos!mod!n_sacd';
$oHash->setCamposForm($campos_form);
$oHash->setcamposNo('lst_ctrs!refresh');
$oHash->setArrayCamposHidden($aCamposHidden);

if ($Qmod === 'nuevo') {
    $txt_btn = _("crear horario");
} else {
    $txt_btn = _("guardar horario");
}

$a_campos = ['oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'url_actualizar' => $url_actualizar,
    'oHash' => $oHash,
    'id_enc' => $Qid_enc,
    'oDesplDia' => $oDesplDia,
    'oDesplMas' => $oDesplMas,
    'oDesplOrd' => $oDesplOrd,
    'oDesplRef' => $oDesplRef,
    'mod' => $Qmod,
    'f_ini' => $f_ini,
    'f_fin' => $f_fin,
    'h_ini' => $h_ini,
    'h_fin' => $h_fin,
    'n_sacd' => $n_sacd,
    'txt_btn' => $txt_btn,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('horario_ver.phtml', $a_campos);
