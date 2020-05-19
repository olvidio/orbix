<?php
use web\Desplegable;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo_teleco = (string) \filter_input(INPUT_POST, 'tipo_teleco');


$oDescTeleco=new ubis\model\entity\GestorDescTeleco();
$aOpciones = $oDescTeleco->getListaDescTelecoUbis($Qtipo_teleco);
$oDesplegableDescTeleco= new Desplegable();
$oDesplegableDescTeleco->setOpciones($aOpciones);
$oDesplegableDescTeleco->setNombre('desc_teleco');
$oDesplegableDescTeleco->setBlanco(true);

echo $oDesplegableDescTeleco->desplegable();