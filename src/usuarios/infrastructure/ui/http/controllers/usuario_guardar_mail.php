<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Email;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_guardar'),
        'usuario_guardar_mail'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = '';

$Qid_usuario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_usuario');
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
