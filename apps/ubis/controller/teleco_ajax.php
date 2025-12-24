<?php

use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use web\Desplegable;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_tipo_teleco = (string)filter_input(INPUT_POST, 'id_tipo_teleco');


$oDescTeleco = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
$aOpciones = $oDescTeleco->getArrayDescTelecoUbis($Qid_tipo_teleco);
$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($aOpciones);
$oDesplegableDescTeleco->setNombre('id_desc_teleco');
$oDesplegableDescTeleco->setBlanco(true);

echo $oDesplegableDescTeleco->desplegable();