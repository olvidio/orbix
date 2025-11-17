<?php

use src\ubis\application\repositories\DescTelecoRepository;
use web\Desplegable;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo_teleco = (string)filter_input(INPUT_POST, 'tipo_teleco');


$oDescTeleco = new DescTelecoRepository();
$aOpciones = $oDescTeleco->getArrayDescTelecoUbis($Qtipo_teleco);
$oDesplegableDescTeleco = new Desplegable();
$oDesplegableDescTeleco->setOpciones($aOpciones);
$oDesplegableDescTeleco->setNombre('desc_teleco');
$oDesplegableDescTeleco->setBlanco(true);

echo $oDesplegableDescTeleco->desplegable();