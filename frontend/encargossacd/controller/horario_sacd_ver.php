<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\encargossacd\helpers\EncargossacdPostInput;

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Horario encargo sacd en ficha. Datos: `/src/encargossacd/horario_sacd_ver_data`
 * (ver {@see \src\encargossacd\application\EncargoSacdHorarioVerData}).
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = EncargossacdPostInput::postInt('id_nom');
$Qid_enc = EncargossacdPostInput::postInt('id_enc');
$Qmod = EncargossacdPostInput::postInt('mod');
$Qfiltro_sacd = EncargossacdPostInput::postString('filtro_sacd');
$Qid_item = EncargossacdPostInput::postInt('id_item');
$Qdesc_enc = EncargossacdPostInput::postString('desc_enc');

/** @var array<string, mixed> $d */
$d = PostRequest::getDataFromUrl('/src/encargossacd/horario_sacd_ver_data', [
    'id_nom' => $Qid_nom,
    'id_enc' => $Qid_enc,
    'id_item' => $Qid_item,
    'desc_enc' => $Qdesc_enc,
]);

$ap_nom = \frontend\shared\helpers\PayloadCoercion::string($d['ap_nom'] ?? '');
$titulo = \frontend\shared\helpers\PayloadCoercion::string($d['titulo'] ?? '');
$id_item = \frontend\shared\helpers\PayloadCoercion::int($d['id_item'] ?? 0);
$desc_enc = \frontend\shared\helpers\PayloadCoercion::string($d['desc_enc'] ?? '');
$f_ini_iso = \frontend\shared\helpers\PayloadCoercion::string($d['f_ini_iso'] ?? '');
$f_fin_iso = \frontend\shared\helpers\PayloadCoercion::string($d['f_fin_iso'] ?? '');
$dia_ref = \frontend\shared\helpers\PayloadCoercion::string($d['dia_ref'] ?? '');
$dia_num = \frontend\shared\helpers\PayloadCoercion::string($d['dia_num'] ?? '');
$mas_menos = \frontend\shared\helpers\PayloadCoercion::string($d['mas_menos'] ?? '');
$dia_inc = \frontend\shared\helpers\PayloadCoercion::string($d['dia_inc'] ?? '');
$h_ini = \frontend\shared\helpers\PayloadCoercion::string($d['h_ini'] ?? '');
$h_fin = \frontend\shared\helpers\PayloadCoercion::string($d['h_fin'] ?? '');
$tiene_excepciones = !empty($d['tiene_excepciones']);
$dia = \frontend\shared\helpers\PayloadCoercion::string($d['dia'] ?? '');
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

AjaxJsonSupport::renderPhtml('frontend\\encargossacd\\controller', 'horario_sacd_ver.phtml', $a_campos);
