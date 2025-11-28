<?php

use permisos\model\MyCrypt;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Password;
use web\ContestarJson;

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oUsuario = $UsuarioRepository->findById($Qid_usuario);
$usuario = $oUsuario->getUsuario();

if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);

    if ($jsondata['success'] === false) {
        ContestarJson::send($jsondata);
        exit();
    } else {
        $my_passwd = $oCrypt->encode($Qpassword);
        $oUsuario->setPassword(new Password($my_passwd));
        $oUsuario->setCambio_password(false);
    }

    if ($UsuarioRepository->Guardar($oUsuario) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');