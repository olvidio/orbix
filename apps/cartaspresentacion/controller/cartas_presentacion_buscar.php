<?php

use core\ViewTwig;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use web\Desplegable;
use web\Hash;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\RegionDropdown;

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
//paises posibles
$GesPais = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);
$aOpciones = $GesPais->getArrayPaises();
$oDesplPais = new Desplegable();
$oDesplPais->setOpciones($aOpciones);
$oDesplPais->setNombre('pais');
//delegaciones de H. posibles
$oDesplDelegacion = DelegacionDropdown::byRegiones(['H'], 'dl');

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

$oView = new ViewTwig('cartaspresentacion/controller');
$oView->renderizar('cartas_presentacion_buscar.html.twig', $a_campos);
