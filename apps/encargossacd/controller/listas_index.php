<?php
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url = 'apps/encargossacd/controller/comprobaciones.php';
$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setcamposForm('que');
$h = $oHash->linkSinVal();

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_a = web\Hash::link('apps/encargossacd/controller/listas_a.php?'.http_build_query($aQuery));
$aQuery = ['sf' => 1];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_af = web\Hash::link('apps/encargossacd/controller/listas_a.php?'.http_build_query($aQuery));

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_b = web\Hash::link('apps/encargossacd/controller/listas_b.php?'.http_build_query($aQuery));
$aQuery = ['sf' => 1];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_bf = web\Hash::link('apps/encargossacd/controller/listas_b.php?'.http_build_query($aQuery));

$url_lista_c = web\Hash::link('apps/encargossacd/controller/listas_c.php');

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_d = web\Hash::link('apps/encargossacd/controller/listas_d.php?'.http_build_query($aQuery));
$aQuery = ['sf' => 1];
$url_lista_df = web\Hash::link('apps/encargossacd/controller/listas_d.php?'.http_build_query($aQuery));


$aQuery = ['ctr_igl' => 'ctr'];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_ctr = web\Hash::link('apps/encargossacd/controller/listas_exigencia_ctr.php?'.http_build_query($aQuery));
$aQuery = ['ctr_igl' => 'ctr', 'sf' => 1];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_ctrf = web\Hash::link('apps/encargossacd/controller/listas_exigencia_ctr.php?'.http_build_query($aQuery));
$aQuery = ['ctr_igl' => 'igl'];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_igl = web\Hash::link('apps/encargossacd/controller/listas_exigencia_ctr.php?'.http_build_query($aQuery));

$aQuery = ['sel' => 'nagd'];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_com_sacd_na = web\Hash::link('apps/encargossacd/controller/listas_com_sacd.php?'.http_build_query($aQuery));
$aQuery = ['sel' => 'sssc'];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_com_sacd_sss = web\Hash::link('apps/encargossacd/controller/listas_com_sacd.php?'.http_build_query($aQuery));

$aQuery = ['sfsv' => 'sv'];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_com_ctr_sv = web\Hash::link('apps/encargossacd/controller/listas_com_ctr.php?'.http_build_query($aQuery));
$aQuery = ['sfsv' => 'sf'];
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_lista_com_ctr_sf = web\Hash::link('apps/encargossacd/controller/listas_com_ctr.php?'.http_build_query($aQuery));

$url_lista_com_txt = web\Hash::link('apps/encargossacd/controller/listas_com_txt.php');


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

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('listas_index.html.twig',$a_campos);
