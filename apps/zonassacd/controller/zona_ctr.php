<?php

use core\ViewTwig;
use web\Hash;
use zonassacd\model\entity\GestorZona;

/**
 * Esta página sirve para asignar centros a una zona de Misas.
 * En la parte superior se permite escojer la zona de misas.
 * En la parte inferior se permite pasar los centros seleccionados a una nueva zona.
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
$aOpciones = $oGestorZona->getArrayZonas();
$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(FALSE);
$oDesplZonas->setBlanco(0);

$url_ajax = 'apps/zonassacd/controller/zona_ctr_ajax.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ajax);
$oHashCtr->setCamposForm('que!id_zona');
$h_ctr = $oHashCtr->linkSinVal();

$oHash = new Hash();
$oHash->setUrl($url_ajax);
$a_camposHidden = ['que' => 'update'];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('id_zona_new');
$oHash->setCamposNo('scroll_id!sel');

$perm_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
    $perm_des = TRUE;
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'h_ctr' => $h_ctr,
    'perm_des' => $perm_des,
    'oDesplZonas' => $oDesplZonas,
];

$oView = new ViewTwig('zonassacd/controller');
$oView->renderizar('zona_ctr.html.twig', $a_campos);
