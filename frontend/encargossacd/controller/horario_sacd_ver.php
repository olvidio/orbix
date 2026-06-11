<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Horario encargo sacd en ficha. Datos: `/src/encargossacd/horario_sacd_ver_data`
 * (ver {@see \src\encargossacd\application\EncargoSacdHorarioVerData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = encargossacd_post_int('id_nom');
$Qid_enc = encargossacd_post_int('id_enc');
$Qmod = encargossacd_post_int('mod');
$Qfiltro_sacd = encargossacd_post_string('filtro_sacd');
$Qid_item = encargossacd_post_int('id_item');
$Qdesc_enc = encargossacd_post_string('desc_enc');

/** @var array<string, mixed> $d */
$d = PostRequest::getDataFromUrl('/src/encargossacd/horario_sacd_ver_data', [
    'id_nom' => $Qid_nom,
    'id_enc' => $Qid_enc,
    'id_item' => $Qid_item,
    'desc_enc' => $Qdesc_enc,
]);

$ap_nom = tessera_imprimir_string($d['ap_nom'] ?? '');
$titulo = tessera_imprimir_string($d['titulo'] ?? '');
$id_item = tessera_imprimir_int($d['id_item'] ?? 0);
$desc_enc = tessera_imprimir_string($d['desc_enc'] ?? '');
$f_ini_iso = tessera_imprimir_string($d['f_ini_iso'] ?? '');
$f_fin_iso = tessera_imprimir_string($d['f_fin_iso'] ?? '');
$dia_ref = tessera_imprimir_string($d['dia_ref'] ?? '');
$dia_num = tessera_imprimir_string($d['dia_num'] ?? '');
$mas_menos = tessera_imprimir_string($d['mas_menos'] ?? '');
$dia_inc = tessera_imprimir_string($d['dia_inc'] ?? '');
$h_ini = tessera_imprimir_string($d['h_ini'] ?? '');
$h_fin = tessera_imprimir_string($d['h_fin'] ?? '');
$tiene_excepciones = !empty($d['tiene_excepciones']);
$dia = tessera_imprimir_string($d['dia'] ?? '');
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

ajax_json_render_phtml('frontend\\encargossacd\\controller', 'horario_sacd_ver.phtml', $a_campos);
