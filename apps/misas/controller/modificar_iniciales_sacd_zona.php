<?php


// INICIO Cabecera global de URL de controlador *********************************
use web\Desplegable;

use misas\domain\repositories\InicialesSacdRepository;
use personas\model\entity\PersonaSacd;
use personas\model\entity\PersonaEx;
use zonassacd\model\entity\GestorZona;
use zonassacd\model\entity\GestorZonaSacd;
use web\Hash;


require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_iniciales_sacd_zona()');

$url_ver_iniciales_zona = 'apps/misas/controller/ver_iniciales_zona.php';
$oHashZona = new Hash();
$oHashZona->setUrl($url_ver_iniciales_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'url_ver_iniciales_zona' => $url_ver_iniciales_zona,
    'h_zona' => $h_zona,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('modificar_iniciales_sacd_zona.html.twig', $a_campos);

