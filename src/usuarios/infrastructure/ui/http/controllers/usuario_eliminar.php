<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuarioEliminar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

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

/** @var usuarioEliminar $useCase */
$useCase = DependencyResolver::get(usuarioEliminar::class);
$result = $useCase->execute($id_usuario);

ContestarJson::enviar($result['error'], $result['data']);
