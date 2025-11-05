<?php

use actividadessacd\model\entity\GestorAtnActivSacdTexto;
use core\ViewTwig;
use src\usuarios\application\repositories\LocalRepository;
use web\Desplegable;
use web\Hash;

/**
 * Esta es para cambiar los textos de comunicación de las actividades a los sacd.
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
    't_propio' => _("titulo: propio"),
    't_f_ini' => _("titulo: f_ini"),
    't_f_fin' => _("titulo: f_fin"),
    't_nombre_ubi' => _("titulo: nombre ubi"),
    't_sfsv' => _("titulo: sfsv"),
    't_actividad' => _("titulo: actividad"),
    't_asistentes' => _("titulo: asistentes"),
    't_encargado' => _("titulo: encargado"),
    't_observ' => _("titulo: observaciones"),
    't_nom_tipo' => _("titulo: nom_tipo"),
];
$oDesplClaves = new Desplegable();
$oDesplClaves->setNombre('clave');
$oDesplClaves->setOpciones($a_Claves);
$oDesplClaves->setOpcion_sel('com_sacd');
$oDesplClaves->setAction('fnjs_get_texto()');

//Idiomas
$LocaleRepository = new  LocalRepository();
$a_locles = $LocaleRepository->getArrayLocales();
$oDesplIdiomas = new Desplegable("idioma", $a_locles, 'es', true);
$oDesplIdiomas->setAction('fnjs_get_texto()');

// para que salga algo
$aWhere = [];
$aWhere['clave'] = 'com_sacd';
$aWhere['idioma'] = 'es';
$oGesAtnActivSacdTexto = new GestorAtnActivSacdTexto();
$cAtnActivSacdTextos = $oGesAtnActivSacdTexto->getAtnActivSacdTextos($aWhere);
$txt = '';
if (count($cAtnActivSacdTextos) > 0) {
    $txt = $cAtnActivSacdTextos[0]->getTexto();
}

$comunicacion = $txt;

$url_ajax = 'apps/actividadessacd/controller/com_sacd_txt_ajax.php';
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

$oView = new ViewTwig('actividadessacd/controller');
$oView->renderizar('com_sacd_txt.html.twig', $a_campos);
