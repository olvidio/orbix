<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\strtoupper_dlb;

/**
 * Es un formulario para introducir las condiciones de búsqueda de los ubis.
 *
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/ubis_buscar_data', []);
$opciones_region = $data['opciones_region'] ?? [];
$opciones_tipo_ctr = $data['opciones_tipo_ctr'] ?? [];
$opciones_tipo_casa = $data['opciones_tipo_casa'] ?? [];
$opciones_pais = $data['opciones_pais'] ?? [];

$Qsimple = (integer)filter_input(INPUT_POST, 'simple');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qloc = (string)filter_input(INPUT_POST, 'loc');

$simple = empty($Qsimple) ? 1 : $Qsimple;
$tipo = empty($Qtipo) ? "tot" : $Qtipo;
$loc = empty($Qloc) ? "tot" : $Qloc;

switch ($tipo) {
    case "ctrdl" :
        $titulo = strtoupper_dlb(_("centros de la delegación"));
        $tituloGros = strtoupper_dlb(_("¿qué centro te interesa?"));
        $nomUbi = ucfirst(_("nombre del centro"));
        break;
    case "vu_ex" :
        $titulo = strtoupper(_("centros o casas de otras dl/r"));
        $tituloGros = strtoupper_dlb(_("¿qué centro o casa te interesa?"));
        $nomUbi = ucfirst(_("nombre del centro o casa"));
        break;
    case "ctrex" :
        $titulo = strtoupper(_("centros de otras dl/r"));
        $tituloGros = strtoupper_dlb(_("¿qué centro te interesa?"));
        $nomUbi = ucfirst(_("nombre del centro"));
        break;
    case "cdcdl" :
        $titulo = strtoupper_dlb(_("casas de la delegación"));
        $tituloGros = strtoupper_dlb(_("¿qué casa te interesa?"));
        $nomUbi = ucfirst(_("nombre de la casa"));
        break;
    case "cdcex" :
        $titulo = strtoupper(_("casas de otras dl/r"));
        $tituloGros = strtoupper_dlb(_("¿qué casa te interesa?"));
        $nomUbi = ucfirst(_("nombre de la casa"));
        break;
    case "mail" :
        $titulo = ucfirst(_("buscar e-mails de los centros de la dl"));
        $tituloGros = ucfirst(_("escoge un grupo de centros"));
        $nomUbi = ucfirst(_("nombre del centro"));
        break;
    case "ctrsf" :
        $titulo = strtoupper(_("centros de la sf"));
        $tituloGros = strtoupper_dlb(_("¿qué centro te interesa?"));
        $nomUbi = ucfirst(_("nombre del centro"));
        break;
}
switch ($tipo) {
    case "ctr" :
        $nomUbi = ucfirst(_("nombre del centro"));
        break;
    case "cdc" :
        $nomUbi = ucfirst(_("nombre de la casa"));
        break;
    case "tot" :
        $nomUbi = ucfirst(_("nombre de la casa o centro"));
        break;
}

$oHash = new HashFront();

$s_camposForm = 'simple!nombre_ubi!opcion!ciudad';
$oHash->setcamposNo('cmb!simple!tipo_ctr!tipo_casa');

if ($simple === 1) {
    $s_camposForm .= '!region!pais';
}
if ($simple === 2) {
    $s_camposForm .= '!tipo!loc';
    if ($loc === "ex") {
        $s_camposForm .= '!dl!region!pais';
    }
}
$oHash->setCamposForm($s_camposForm);


if ($simple === 1) {
    $pagina = HashFront::link('frontend/ubis/controller/ubis_buscar.php?' . http_build_query(array('simple' => '2')));
} else {
    $pagina = HashFront::link('frontend/ubis/controller/ubis_buscar.php?' . http_build_query(array('simple' => '1')));
}

$a_campos = [
    'oHash' => $oHash,
    'tipo' => $tipo,
    'simple' => $simple,
    'nomUbi' => $nomUbi,
    'opciones_region' => $opciones_region,
    'opciones_pais' => $opciones_pais,
    'loc' => $loc,
    'opciones_tipo_casa' => $opciones_tipo_casa,
    'opciones_tipo_ctr' => $opciones_tipo_ctr,
    'pagina' => $pagina,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_buscar.phtml', $a_campos);