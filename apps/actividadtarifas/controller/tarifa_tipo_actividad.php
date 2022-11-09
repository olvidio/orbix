<?php
/**
 * Esta página muestra la lista de los tipos de actividades y sus tarifas asociadas.
 * Desde aqui se pueden modificar las tarifas asociadas, o crear nuevas asociaciones.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        24/2/09.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_tipo_actividad_ajax.php');
$oHash->setCamposForm('que');
$h_ver = $oHash->linkSinVal();

$oHashMod = new web\Hash();
$oHashMod->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadtarifas/controller/tarifa_tipo_actividad_form.php');
$oHashMod->setCamposForm('id_item');
$h_modificar = $oHashMod->linkSinVal();

$txt_eliminar = _("¿Está seguro que desea quitar esta id_tarifa?");

$a_campos = ['oPosicion' => $oPosicion,
    'h_ver' => $h_ver,
    'h_modificar' => $h_modificar,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new core\View('actividadtarifas/controller');
$oView->renderizar('tarifa_tipo_actividad.phtml', $a_campos);