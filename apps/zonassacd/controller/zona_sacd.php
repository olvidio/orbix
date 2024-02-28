<?php

use web\Hash;
use zonassacd\model\entity\GestorZona;

/**
 * Esta página sirve para asignar sacd a una zona de Misas.
 * En la parte superior se permite escojer la zona de misas.
 * En la parte inferior se permite pasar los sacd seleccionados a una nueva zona.
 *
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        16/11/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$GesZonas = new GestorZona();
$oDesplZonas = $GesZonas->getListaZonas();
$oDesplZonas->setBlanco(0);
$url_ajax = 'apps/zonassacd/controller/zona_sacd_ajax.php';

$oHashSacd = new Hash();
$oHashSacd->setUrl($url_ajax);
$oHashSacd->setCamposForm('que!id_zona');
$h_sacd = $oHashSacd->linkSinVal();

$oHashUrlGet = new Hash();
$oHashUrlGet->setUrl('apps/misas/controller/zona_sacd_datos_get.php');
$oHashUrlGet->setCamposForm('id_sacd!id_zona');
$h_url_get = $oHashUrlGet->linkSinVal();

$oHashUrlPut = new Hash();
$oHashUrlPut->setUrl('apps/misas/controller/zona_sacd_datos_put.php');
$oHashUrlPut->setCamposForm('id_sacd!id_zona!dw1!dw2!dw3!dw4!dw5!dw6!dw7');
$h_url_put = $oHashUrlPut->linkSinVal();



$oHash = new Hash();
$oHash->setUrl($url_ajax);
$a_camposHidden = ['acumular' => 0, 'que' => 'update'];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('id_zona!id_zona_new');
$oHash->setCamposNo('acumular!scroll_id!sel');

$perm_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
    $perm_des = TRUE;
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_sacd' => $h_sacd,
    'h_url_get' => $h_url_get,
    'h_url_put' => $h_url_put,
    'perm_des' => $perm_des,
    'oDesplZonas' => $oDesplZonas,
];

$oView = new core\ViewTwig('zonassacd/controller');
$oView->renderizar('zona_sacd.html.twig', $a_campos);
