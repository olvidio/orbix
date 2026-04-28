<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

/**
 * Muestra las ausencias de un SACD.
 *
 * Capa frontend del slice. Los datos se obtienen de
 * `/src/encargossacd/sacd_ausencias_get_data`
 * ({@see \src\encargossacd\application\SacdAusenciasGetData}) y la vista
 * `sacd_ausencias_get.phtml` se limita a presentacion.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qhistorial = (integer)filter_input(INPUT_POST, 'historial');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qfiltro_sacd = (integer)filter_input(INPUT_POST, 'filtro_sacd');

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
    $id_enc[$i] = (int)($fila['id_enc'] ?? 0);
    $id_tipo_enc[$i] = (int)($fila['id_tipo_enc'] ?? 0);
    $desc_enc[$i] = (string)($fila['desc_enc'] ?? '');
    $id_item[$i] = (int)($fila['id_item'] ?? 0);
    $inicio[$i] = (string)($fila['inicio'] ?? '');
    $fin[$i] = (string)($fila['fin'] ?? '');
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

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('sacd_ausencias_get.phtml', $a_campos);
