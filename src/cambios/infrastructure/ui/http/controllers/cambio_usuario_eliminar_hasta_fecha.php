<?php


/**
 * Endpoint backend: elimina los `CambioUsuario` con fecha <= `f_fin`.
 *
 * Mutación global (no se filtra por `id_usuario`): cápsula acción-only, sin
 * contexto, emitida por `AvisosGenerarListaData`.
 */

use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar_fecha'),
        'cambio_usuario_eliminar_hasta_fecha'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = ['f_fin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_fin')];

/** @var CambioUsuarioEliminarHastaFecha $useCase */
$useCase = DependencyResolver::get(CambioUsuarioEliminarHastaFecha::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
