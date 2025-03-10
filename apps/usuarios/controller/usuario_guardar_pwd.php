<?php

use permisos\model\MyCrypt;
use usuarios\model\entity\Usuario;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');
$Qpass = (string)filter_input(INPUT_POST, 'pass');

$oUsuario = new Usuario(array('id_usuario' => $Qid_usuario));
$oUsuario->DBCarregar();

$usuario = $oUsuario->getUsuario();

if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);

    if ($jsondata['success'] === FALSE) {
        ContestarJson::send($jsondata);
        exit();
    } else {
        $my_passwd = $oCrypt->encode($Qpassword);
        $oUsuario->setPassword($my_passwd);
    }
} else {
    $oUsuario->setPassword($Qpass);
}

if ($oUsuario->DBGuardar() === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $oUsuario->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');