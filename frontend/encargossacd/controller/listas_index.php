<?php

use frontend\shared\model\ViewNewPhtml;
use web\Hash;

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/**
 * Construye la URL firmada a un controlador de `frontend/encargossacd/controller`
 * con los parametros dados (filtra nulls con `poner_empty_on_null`).
 *
 * @param array<string, int|string|null> $params
 */
$lnk = static function (string $script, array $params = []): string {
    array_walk($params, 'src\shared\domain\helpers\poner_empty_on_null');
    $url = 'frontend/encargossacd/controller/' . $script;
    if ($params !== []) {
        $url .= '?' . http_build_query($params);
    }

    return Hash::link($url);
};

$url = 'frontend/encargossacd/controller/comprobaciones.php';
$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('que');
$h = $oHash->linkSinValParams();

$url_lista_a = $lnk('listas_a.php', ['sf' => 0]);
$url_lista_af = $lnk('listas_a.php', ['sf' => 1]);
$url_lista_b = $lnk('listas_b.php', ['sf' => 0]);
$url_lista_bf = $lnk('listas_b.php', ['sf' => 1]);
$url_lista_c = $lnk('listas_c.php');
$url_lista_d = $lnk('listas_d.php', ['sf' => 0]);
$url_lista_df = $lnk('listas_d.php', ['sf' => 1]);

$url_lista_ctr = $lnk('listas_exigencia_ctr.php', ['ctr_igl' => 'ctr']);
$url_lista_ctrf = $lnk('listas_exigencia_ctr.php', ['ctr_igl' => 'ctr', 'sf' => 1]);
$url_lista_igl = $lnk('listas_exigencia_ctr.php', ['ctr_igl' => 'igl']);

$url_lista_com_sacd_na = $lnk('listas_com_sacd.php', ['sel' => 'nagd']);
$url_lista_com_sacd_sss = $lnk('listas_com_sacd.php', ['sel' => 'sssc']);

$url_lista_com_ctr_sv = $lnk('listas_com_ctr.php', ['sfsv' => 'sv']);
$url_lista_com_ctr_sf = $lnk('listas_com_ctr.php', ['sfsv' => 'sf']);

$url_lista_com_txt = $lnk('listas_com_txt.php');

$a_campos = [
    'oPosicion' => $oPosicion,
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

$oView = new ViewNewPhtml('frontend\\encargossacd\\controller');
$oView->renderizar('listas_index.phtml', $a_campos);
