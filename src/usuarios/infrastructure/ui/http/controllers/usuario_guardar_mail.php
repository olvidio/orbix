<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Email;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qid_usuario = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_usuario');
$Qemail = (string)\src\shared\domain\helpers\FilterPostGet::post('email', FILTER_VALIDATE_EMAIL);

$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oUsuario = $UsuarioRepository->findById($Qid_usuario);
if ($oUsuario === null) {
    ContestarJson::enviar(_('Usuario no encontrado'), 'none');
    return;
}

$email = new Email($Qemail);
$oUsuario->setEmailVo($email);
if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');