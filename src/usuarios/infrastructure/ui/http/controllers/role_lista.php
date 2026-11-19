<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\rolesLista;
use src\shared\web\ContestarJson;

// Administración de usuarios, grupos y permisos: solo los roles de administración.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var rolesLista $useCase */
$useCase = DependencyResolver::get(rolesLista::class);
$jsondata = $useCase->execute();

ContestarJson::send($jsondata);
