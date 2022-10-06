<?php
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$Qaccion = (string)filter_input(INPUT_POST, 'accion');
$Qvalor_depende = (string)filter_input(INPUT_POST, 'valor_depende');

/***************  datos  **********************************/

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info);
$oDatos = new $obj();
// datos del dossier:
//$oDatosDossier = new dossiers\DatosDossier($Qid_dossier);
//$oDatos = $oDatosDossier->getDatos();
$oDatos->setAccion($Qaccion);

echo $oDatos->getAccion($Qvalor_depende);