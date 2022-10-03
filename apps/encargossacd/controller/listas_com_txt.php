<?php

use encargossacd\model\entity\GestorEncargoTexto;
use web\Desplegable;
use web\Hash;

/**
 * Esta es para cambiar los textos de comunicación de los encargos a los sacd.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        12/12/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// claves:
$a_Claves = ["com_sacd" => _("comunicación a los sacerdotes"),
    "com_ctr" => _("comunicación a los centros"),
    "t_titular" => _("titulo: titular"),
    "t_secc" => _("titulo: sección"),
    "t_mañana" => _("titulo: mañana"),
    "t_tarde1" => _("titulo: tarde 1ª hora"),
    "t_tarde2" => _("titulo: tarde 2ª hora"),
    "t_mañanas" => _("titulo (plural): mañanas"),
    "t_tardes1" => _("titulo (plural): tardes 1ª hora"),
    "t_tardes2" => _("titulo (plural): tardes 2ª hora"),
    "t_suplente" => _("titulo: suplente"),
    "t_colaborador" => _("titulo: colaborador"),
    "t_otros" => _("titulo: otros"),
    "t_observ" => _("titulo: observaciones"),
];
$oDesplClaves = new Desplegable();
$oDesplClaves->setNombre('clave');
$oDesplClaves->setOpciones($a_Claves);
$oDesplClaves->setOpcion_sel('com_sacd');
$oDesplClaves->setAction('fnjs_get_texto()');

//Idiomas
$GesLocales = new usuarios\model\entity\GestorLocal();
$oDesplIdiomas = $GesLocales->getListaIdiomas();
$oDesplIdiomas->setNombre("idioma");
$oDesplIdiomas->setOpcion_sel('es');
$oDesplIdiomas->setAction('fnjs_get_texto()');

// para que salga algo
$aWhere = [];
$aWhere['clave'] = 'com_sacd';
$aWhere['idioma'] = 'es';
$oGesEncargoTextos = new GestorEncargoTexto();
$cEncargoTextos = $oGesEncargoTextos->getEncargoTextos($aWhere);
$txt = '';
if (count($cEncargoTextos) > 0) {
    $txt = $cEncargoTextos[0]->getTexto();
}

$comunicacion = $txt;

$url_ajax = 'apps/encargossacd/controller/listas_com_txt_ajax.php';
$oHash = new Hash();
$oHash->setUrl($url_ajax);
$aCamposHidden = ['que' => 'update'];
$oHash->setArrayCamposHidden($aCamposHidden);
$oHash->setCamposForm("comunicacion!clave!idioma");

$oHashGet = new Hash();
$oHashGet->setUrl($url_ajax);
$oHashGet->setCamposForm("que!clave!idioma");
$h_get = $oHashGet->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_get' => $h_get,
    'comunicacion' => $comunicacion,
    'oDesplClaves' => $oDesplClaves,
    'oDesplIdiomas' => $oDesplIdiomas,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('listas_com_txt.html.twig', $a_campos);
