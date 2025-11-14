<?php

use core\ViewPhtml;
use src\ubis\application\services\RegionDropdown;
use src\ubis\application\services\TipoCasaDropdown;
use src\ubis\application\services\TipoCentroDropdown;
use ubis\model\entity\GestorDireccionCtr;
use web\Desplegable;
use web\Hash;
use function core\strtoupper_dlb;

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
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//regiones posibles
$oDesplRegion = RegionDropdown::activasOrdenNombre('region');

// tipo ctr
$oDesplTipoCentro = TipoCentroDropdown::listaTiposCentro(true, 'tipo_ctr');

// tipo casa
$oDesplTipoCasa = TipoCasaDropdown::listaTiposCasa(true, 'tipo_casa');

//paises posibles
$GesPais = new GestorDireccionCtr();
$oDesplPais = $GesPais->getListaPaises();
$oDesplPais->setNombre('pais');

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

$oHash = new Hash();

$s_camposForm = 'simple!nombre_ubi!opcion!ciudad';
$oHash->setcamposNo('cmb!simple!tipo_ctr!tipo_casa');

if ($simple == 1) {
    $s_camposForm .= '!region!pais';
}
if ($simple == 2) {
    $s_camposForm .= '!tipo!loc';
    if ($loc == "ex") {
        $s_camposForm .= '!dl!region!pais';
    }
}
$oHash->setCamposForm($s_camposForm);


if ($simple == 1) {
    $pagina = Hash::link('apps/ubis/controller/ubis_buscar.php?' . http_build_query(array('simple' => '2')));
} else {
    $pagina = Hash::link('apps/ubis/controller/ubis_buscar.php?' . http_build_query(array('simple' => '1')));
}

$a_campos = [
    'oHash' => $oHash,
    'tipo' => $tipo,
    'simple' => $simple,
    'nomUbi' => $nomUbi,
    'oDesplRegion' => $oDesplRegion,
    'oDesplPais' => $oDesplPais,
    'loc' => $loc,
    'oDesplTipoCasa' => $oDesplTipoCasa,
    'oDesplTipoCentro' => $oDesplTipoCentro,
    'pagina' => $pagina,
];

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('ubis_buscar.phtml', $a_campos);