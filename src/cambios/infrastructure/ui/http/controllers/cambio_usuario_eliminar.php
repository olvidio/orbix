<?php


/**
 * Endpoint backend: elimina `CambioUsuario` por la clave compuesta
 * `id_item_cambio#id_usuario#sfsv#aviso_tipo` recibida en `sel[]`.
 *
 * Cada valor de `sel[]` es una cápsula `HashB` (acción `cambio_usuario_eliminar`)
 * emitida por fila en `AvisosGenerarListaData`, no la clave compuesta en claro.
 */

use src\cambios\application\CambioUsuarioEliminar;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$a_sel_raw = FuncTablasSupport::inputStringList($_POST, 'sel');

$a_sel = [];
foreach ($a_sel_raw as $capsule) {
    try {
        $ctx = HashB::open($capsule, 'cambio_usuario_eliminar');
    } catch (HashBInvalidException $e) {
        continue;
    }
    $a_sel[] = sprintf(
        '%d#%d#%d#%d',
        FuncTablasSupport::inputInt($ctx, 'id_item_cambio'),
        FuncTablasSupport::inputInt($ctx, 'id_usuario'),
        FuncTablasSupport::inputInt($ctx, 'sfsv'),
        FuncTablasSupport::inputInt($ctx, 'aviso_tipo')
    );
}

$input = ['sel' => $a_sel];

/** @var CambioUsuarioEliminar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioEliminar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
