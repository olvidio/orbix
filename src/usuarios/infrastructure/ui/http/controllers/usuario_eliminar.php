<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuarioEliminar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

// Administración de usuarios, grupos y permisos: solo los roles de administración.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
if ($a_sel === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    $ctx = HashB::open($a_sel[0], 'usuario_eliminar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$id_usuario = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_usuario');
// A un superadministrador solo lo borra otro superadministrador.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permiteSobreUsuario($id_usuario)) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var usuarioEliminar $useCase */
$useCase = DependencyResolver::get(usuarioEliminar::class);
$result = $useCase->execute($id_usuario);

ContestarJson::enviar($result['error'], $result['data']);
