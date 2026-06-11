<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

/**
 * Muestra las ausencias de un SACD.
 *
 * Capa frontend del slice. Los datos se obtienen de
 * `/src/encargossacd/sacd_ausencias_get_data`
 * ({@see \src\encargossacd\application\SacdAusenciasGetData}) y la vista
 * `sacd_ausencias_get.phtml` se limita a presentacion.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qhistorial = encargossacd_post_int('historial');
$Qid_nom = encargossacd_post_int('id_nom');
$Qfiltro_sacd = encargossacd_post_int('filtro_sacd');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/sacd_ausencias_get_data', [
    'id_nom' => $Qid_nom,
    'historial' => $Qhistorial,
]);
$array_tipo_ausencias = is_array($datos['array_tipo_ausencias'] ?? null) ? $datos['array_tipo_ausencias'] : [];
$filas = is_array($datos['filas'] ?? null) ? $datos['filas'] : [];

$enc_num = count($filas);
$id_enc = [];
$id_tipo_enc = [];
$desc_enc = [];
$id_item = [];
$inicio = [];
$fin = [];
foreach ($filas as $i => $fila) {
    $row = encargossacd_ausencia_row($fila);
    $id_enc[$i] = $row['id_enc'];
    $id_tipo_enc[$i] = $row['id_tipo_enc'];
    $desc_enc[$i] = $row['desc_enc'];
    $id_item[$i] = $row['id_item'];
    $inicio[$i] = $row['inicio'];
    $fin[$i] = $row['fin'];
}

$a_cosas = [
    'id_nom' => $Qid_nom,
    'filtro_sacd' => $Qfiltro_sacd,
    'historial' => 1,
];
$go_to = HashFront::link('frontend/encargossacd/controller/sacd_ausencias_get.php?' . http_build_query($a_cosas));
$lnk_historia = "<span class='link' onclick=\"fnjs_update_div('#ficha','$go_to');\">" . _("ver anteriores") . "</span>";

$url_update = "frontend/encargossacd/controller/sacd_ausencias_update.php";
$oHash = new HashFront();
$aCamposHidden = [
    "enc_num" => $enc_num,
    "id_nom" => $Qid_nom,
    "filtro_sacd" => $Qfiltro_sacd,
];
$oHash->setUrl($url_update);
$oHash->setCamposForm('id_item!id_enc!fin!inicio');
$oHash->setcamposNo('enc_num!id_item!refresh!mas');
$oHash->setArrayCamposHidden($aCamposHidden);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_update' => $url_update,
    'lnk_historia' => $lnk_historia,
    'enc_num' => $enc_num,
    'id_tipo_enc' => $id_tipo_enc,
    'id_enc' => $id_enc,
    'id_item' => $id_item,
    'desc_enc' => $desc_enc,
    'inicio' => $inicio,
    'fin' => $fin,
    'array_tipo_ausencias' => $array_tipo_ausencias,
];

ajax_json_render_phtml('frontend\\encargossacd\\controller', 'sacd_ausencias_get.phtml', $a_campos);
