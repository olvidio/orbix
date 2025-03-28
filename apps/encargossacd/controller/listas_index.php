<?php

use core\ViewTwig;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url = 'apps/encargossacd/controller/comprobaciones.php';
$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('que');
$h = $oHash->linkSinVal();

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_a = Hash::link('apps/encargossacd/controller/listas_a.php?' . http_build_query($aQuery));
$aQuery = ['sf' => 1];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_af = Hash::link('apps/encargossacd/controller/listas_a.php?' . http_build_query($aQuery));

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_b = Hash::link('apps/encargossacd/controller/listas_b.php?' . http_build_query($aQuery));
$aQuery = ['sf' => 1];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_bf = Hash::link('apps/encargossacd/controller/listas_b.php?' . http_build_query($aQuery));

$url_lista_c = Hash::link('apps/encargossacd/controller/listas_c.php');

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_d = Hash::link('apps/encargossacd/controller/listas_d.php?' . http_build_query($aQuery));
$aQuery = ['sf' => 1];
$url_lista_df = Hash::link('apps/encargossacd/controller/listas_d.php?' . http_build_query($aQuery));


$aQuery = ['ctr_igl' => 'ctr'];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_ctr = Hash::link('apps/encargossacd/controller/listas_exigencia_ctr.php?' . http_build_query($aQuery));
$aQuery = ['ctr_igl' => 'ctr', 'sf' => 1];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_ctrf = Hash::link('apps/encargossacd/controller/listas_exigencia_ctr.php?' . http_build_query($aQuery));
$aQuery = ['ctr_igl' => 'igl'];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_igl = Hash::link('apps/encargossacd/controller/listas_exigencia_ctr.php?' . http_build_query($aQuery));

$aQuery = ['sel' => 'nagd'];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_com_sacd_na = Hash::link('apps/encargossacd/controller/listas_com_sacd.php?' . http_build_query($aQuery));
$aQuery = ['sel' => 'sssc'];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_com_sacd_sss = Hash::link('apps/encargossacd/controller/listas_com_sacd.php?' . http_build_query($aQuery));

$aQuery = ['sfsv' => 'sv'];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_com_ctr_sv = Hash::link('apps/encargossacd/controller/listas_com_ctr.php?' . http_build_query($aQuery));
$aQuery = ['sfsv' => 'sf'];
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$url_lista_com_ctr_sf = Hash::link('apps/encargossacd/controller/listas_com_ctr.php?' . http_build_query($aQuery));

$url_lista_com_txt = Hash::link('apps/encargossacd/controller/listas_com_txt.php');


$a_campos = ['oPosicion' => $oPosicion,
    'url' => $url,
    'h' => $h,
    'url_lista_a' => $url_lista_a,
    'url_lista_af' => $url_lista_af,
    'url_lista_b' => $url_lista_b,
    'url_lista_bf' => $url_lista_bf,
    'url_lista_c' => $url_lista_c,
    'url_lista_d' => $url_lista_d,
    'url_lista_df' => $url_lista_df,
    'url_lista_ctr' => $url_lista_ctr,
    'url_lista_ctrf' => $url_lista_ctrf,
    'url_lista_igl' => $url_lista_igl,
    'url_lista_com_sacd_na' => $url_lista_com_sacd_na,
    'url_lista_com_sacd_sss' => $url_lista_com_sacd_sss,
    'url_lista_com_ctr_sv' => $url_lista_com_ctr_sv,
    'url_lista_com_ctr_sf' => $url_lista_com_ctr_sf,
    'url_lista_com_txt' => $url_lista_com_txt,
];

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('listas_index.html.twig', $a_campos);
