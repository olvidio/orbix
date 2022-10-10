<?php

use ubis\model\entity\GestorDelegacion;
use ubis\model\entity\GestorDireccion;
use ubis\model\entity\GestorRegion;
use web\Hash;
use ubis\model\entity\GestorDireccionCtr;

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
$GesRegion = new GestorRegion();
$oDesplRegion = $GesRegion->getListaRegiones();
$oDesplRegion->setNombre('region');
//paises posibles
$GesPais = new GestorDireccionCtr();
$oDesplPais = $GesPais->getListaPaises();
$oDesplPais->setNombre('pais');
//delegaciones de H. posibles
$GesDelegacion = new GestorDelegacion();
$oDesplDelegacion = $GesDelegacion->getListaDelegaciones(['H']);
$oDesplDelegacion->setNombre('dl');

$url_lista = 'apps/cartaspresentacion/controller/cartas_presentacion_lista.php';
$oHash = new Hash();
$oHash->setUrl($url_lista);
$oHash->setArrayCamposHidden(['que' => 'get']);
$oHash->setCamposForm('que!poblacion!region!pais!dl');
$oHash->setCamposNo('scroll_id!sel');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_lista' => $url_lista,
    'oDesplRegion' => $oDesplRegion,
    'oDesplPais' => $oDesplPais,
    'oDesplDelegacion' => $oDesplDelegacion,
];

$oView = new core\ViewTwig('cartaspresentacion/controller');
echo $oView->render('cartas_presentacion_buscar.html.twig', $a_campos);
