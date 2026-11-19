<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuariosLista;
use src\shared\web\ContestarJson;

// Administración de usuarios, grupos y permisos: solo los roles de administración.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qusername = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'username');

/** @var usuariosLista $useCase */
$useCase = DependencyResolver::get(usuariosLista::class);
$jsondata = $useCase->execute($Qusername);

ContestarJson::send($jsondata);
