<?php

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\GruposLista;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\web\ContestarJson;

// Administración de usuarios, grupos y permisos: solo los roles de administración.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qusername = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'username');

$error_txt = '';

/** @var UsuarioRepositoryInterface $usuarioRepository */
$usuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oMiUsuario = $usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
if ($oMiUsuario === null) {
    ContestarJson::enviar(_("Usuario no encontrado"), []);
    return;
}
$miRole = $oMiUsuario->getId_role();

if ($miRole > 3) {
    $error_txt = _("no tiene permisos para ver esto");
}

/** @var GruposLista $useCase */
$useCase = DependencyResolver::get(GruposLista::class);
$data = $useCase->execute($Qusername);

ContestarJson::enviar($error_txt, $data);
