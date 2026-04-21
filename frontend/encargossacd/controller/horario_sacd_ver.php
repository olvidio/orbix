<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

/**
 * Horario encargo sacd en ficha. Datos: `/src/encargossacd/horario_sacd_ver_data`
 * (ver {@see \src\encargossacd\application\EncargoSacdHorarioVerData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qmod = (integer)filter_input(INPUT_POST, 'mod');
$Qfiltro_sacd = (string)filter_input(INPUT_POST, 'filtro_sacd');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');

/** @var array<string, mixed> $d */
$d = PostRequest::getDataFromUrl('/src/encargossacd/horario_sacd_ver_data', [
    'id_nom' => $Qid_nom,
    'id_enc' => $Qid_enc,
    'id_item' => $Qid_item,
    'desc_enc' => $Qdesc_enc,
]);

$ap_nom = (string)($d['ap_nom'] ?? '');
$titulo = (string)($d['titulo'] ?? '');
$id_item = (int)($d['id_item'] ?? 0);
$desc_enc = (string)($d['desc_enc'] ?? '');
$f_ini_iso = (string)($d['f_ini_iso'] ?? '');
$f_fin_iso = (string)($d['f_fin_iso'] ?? '');
$dia_ref = (string)($d['dia_ref'] ?? '');
$dia_num = (string)($d['dia_num'] ?? '');
$mas_menos = (string)($d['mas_menos'] ?? '');
$dia_inc = (string)($d['dia_inc'] ?? '');
$h_ini = (string)($d['h_ini'] ?? '');
$h_fin = (string)($d['h_fin'] ?? '');
$tiene_excepciones = !empty($d['tiene_excepciones']);
$dia = (string)($d['dia'] ?? '');
$opciones_dia_semana = is_array($d['opciones_dia_semana'] ?? null) ? $d['opciones_dia_semana'] : [];
$opciones_dia_ref = is_array($d['opciones_dia_ref'] ?? null) ? $d['opciones_dia_ref'] : [];
$opciones_ordinales = is_array($d['opciones_ordinales'] ?? null) ? $d['opciones_ordinales'] : [];

$url_update = 'frontend/encargossacd/controller/horario_sacd_update.php';

$a_campos = [
    'url_update' => $url_update,
    'Qfiltro_sacd' => $Qfiltro_sacd,
    'Qid_nom' => $Qid_nom,
    'Qid_enc' => $Qid_enc,
    'Qmod' => $Qmod,
    'ap_nom' => $ap_nom,
    'titulo' => $titulo,
    'id_item' => $id_item,
    'desc_enc' => $desc_enc,
    'f_ini_iso' => $f_ini_iso,
    'f_fin_iso' => $f_fin_iso,
    'dia_ref' => $dia_ref,
    'dia_num' => $dia_num,
    'mas_menos' => $mas_menos,
    'dia_inc' => $dia_inc,
    'h_ini' => $h_ini,
    'h_fin' => $h_fin,
    'tiene_excepciones' => $tiene_excepciones,
    'dia' => $dia,
    'opciones_dia_semana' => $opciones_dia_semana,
    'opciones_dia_ref' => $opciones_dia_ref,
    'opciones_ordinales' => $opciones_ordinales,
];

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('horario_sacd_ver.phtml', $a_campos);
