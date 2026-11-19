<?php

use src\procesos\application\UsuarioPermActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

// Permisos de actividades de un usuario: solo los roles de administración de usuarios.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var UsuarioPermActivData $useCase */
$useCase = DependencyResolver::get(UsuarioPermActivData::class);

ContestarJson::enviar('', $useCase->execute($_POST));
