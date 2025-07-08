<?php

use permisos\model\MyCrypt;
use src\usuarios\application\repositories\UsuarioRepository;
use src\usuarios\domain\value_objects\Password;
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

$UsuarioRepository = new UsuarioRepository();
$oUsuario = $UsuarioRepository->findById($Qid_usuario);
$usuario = $oUsuario->getUsuario();

if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);

    if ($jsondata['success'] === FALSE) {
        ContestarJson::send($jsondata);
        exit();
    } else {
        $my_passwd = $oCrypt->encode($Qpassword);
        $oUsuario->setPassword(new Password($my_passwd));
        $oUsuario->setCambio_password(FALSE);
    }

    if ($UsuarioRepository->Guardar($oUsuario) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');