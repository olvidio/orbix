<?php
use src\shared\infrastructure\DependencyResolver;

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\value_objects\Password;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');

$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oUsuario = $UsuarioRepository->findById($Qid_usuario);
if ($oUsuario === null) {
    ContestarJson::enviar(_('Usuario no encontrado'), 'none');
    return;
}
$usuario = $oUsuario->getUsuarioVo();

if (!empty($Qpassword)) {
    $oCrypt = new PasswordHasher();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);

    if ($jsondata['success'] === false) {
        ContestarJson::send($jsondata);
        exit();
    } else {
        $my_passwd = $oCrypt->encode($Qpassword);
        $oUsuario->setPasswordVo(new Password($my_passwd));
        $oUsuario->setCambio_password(false);
    }

    if ($UsuarioRepository->Guardar($oUsuario) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');