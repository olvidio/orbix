<?php

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Email;
use web\ContestarJson;

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qemail = (string)filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oUsuario = $UsuarioRepository->findById($Qid_usuario);

$email = new Email($Qemail);
$oUsuario->setEmail($email);
if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');