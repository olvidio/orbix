<?php

use personas\model\entity\Persona;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_nom = (string)filter_input(INPUT_POST, 'id_nom');

$error_txt = '';

$oPersona = Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $error_txt .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
    $data = 'ok';
} else {
    $data['nombreApellidos'] = $oPersona->getNombreApellidos();
    $data['lugar_nacimiento'] = $oPersona->getLugar_nacimiento();
    $data['f_nacimiento'] = $oPersona->getF_nacimiento()->getFechaLatin();
    $data['nivel_stgr'] = $oPersona->getStgr();

    $data['region_latin'] = $_SESSION['oConfig']->getNomRegionLatin();
    $data['vstgr'] = $_SESSION['oConfig']->getNomVstgr();
    $data['dir_stgr'] = $_SESSION['oConfig']->getDirStgr();
    $data['lugar_firma'] = $_SESSION['oConfig']->getLugarFirma();
    $data['contador'] = $_SESSION['oConfig']->getContador_certificados();
}


// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);