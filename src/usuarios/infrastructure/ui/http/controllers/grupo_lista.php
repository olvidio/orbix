<?php

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\GruposLista;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qusername = FuncTablasSupport::inputString($_POST, 'username');

$error_txt = '';

/** @var UsuarioRepositoryInterface $usuarioRepository */
$usuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oMiUsuario = $usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
if ($oMiUsuario === null) {
    ContestarJson::enviar(_('Usuario no encontrado'), []);
    return;
}
$miRole = $oMiUsuario->getId_role();

if ($miRole > 3) {
    $error_txt = _('no tiene permisos para ver esto');
}

/** @var GruposLista $useCase */
$useCase = DependencyResolver::get(GruposLista::class);
$data = $useCase->execute($Qusername);

ContestarJson::enviar($error_txt, $data);
