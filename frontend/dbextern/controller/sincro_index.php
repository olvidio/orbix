<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use src\shared\config\ConfigGlobal;

require_once 'frontend/shared/global_header_front.inc';

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo');

$data = PostRequest::getDataFromUrl('/src/dbextern/sincro_index_datos', ['tipo' => $tipo_persona]);

if (!empty($data['error'])) {
    exit($data['error']);
}

$region = $data['region'];
$dl_listas = $data['dl_listas'];
$fecha_actualizacion = $data['fecha_actualizacion'];

// Firmar link_specs
$ver_2 = HashFrontSignedLink::fromSpec($data['link_spec_ver_traslados']);
$ver_3 = HashFrontSignedLink::fromSpec($data['link_spec_ver_desaparecidos_orbix']);
$ver_456 = HashFrontSignedLink::fromSpec($data['link_spec_ver_listas']);
$ver_7 = HashFrontSignedLink::fromSpec($data['link_spec_ver_orbix_otradl']);
$ver_8 = HashFrontSignedLink::fromSpec($data['link_spec_ver_desaparecidos_listas']);
$ver_910 = HashFrontSignedLink::fromSpec($data['link_spec_ver_orbix']);
$url_actualizar = HashFrontSignedLink::fromSpec($data['link_spec_self']);

// Hash para AJAX syncro
$url_sincro_syncro = ConfigGlobal::getWeb() . '/src/dbextern/sincro_syncro';
$oHash1 = new HashFront();
$oHash1->setUrl($url_sincro_syncro);
$oHash1->setCamposForm('region!dl_listas!tipo_persona');
$h1 = $oHash1->linkSinValParams();

// Hash para AJAX refrescar
$url_refrescar = ConfigGlobal::getWeb() . '/src/dbextern/refrescar_bdu';
$oHash2 = new HashFront();
$oHash2->setUrl($url_refrescar);
$oHash2->setCamposForm('que');
$h2 = $oHash2->linkSinValParams();

$a_campos = [
    'fecha_actualizacion' => $fecha_actualizacion,
    'region' => $region,
    'dl_listas' => $dl_listas,
    'tipo_persona' => $tipo_persona,
    'p1_unidas_dl' => $data['p1_unidas_dl'],
    'p2_unidas_otradl' => $data['p2_unidas_otradl'],
    'p3_unidas_desaparecidas' => $data['p3_unidas_desaparecidas'],
    'p456_listas_no_unidas' => $data['p456_listas_no_unidas'],
    'p7_orbix_unidas_otra_dl' => $data['p7_orbix_unidas_otra_dl'],
    'p8_orbix_unidas_desaparecidas' => $data['p8_orbix_unidas_desaparecidas'],
    'p910_orbix_no_unidas' => $data['p910_orbix_no_unidas'],
    'ver_2' => $ver_2,
    'ver_3' => $ver_3,
    'ver_456' => $ver_456,
    'ver_7' => $ver_7,
    'ver_8' => $ver_8,
    'ver_910' => $ver_910,
    'url_actualizar' => $url_actualizar,
    'url_sincro_syncro' => $url_sincro_syncro,
    'url_refrescar' => $url_refrescar,
    'h1' => $h1,
    'h2' => $h2,
];

$oView = new ViewNewPhtml('frontend/dbextern/controller');
$oView->renderizar(__FILE__, $a_campos);
