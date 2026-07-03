<?php


/**
 * Endpoint backend: elimina `CambioUsuario` por la clave compuesta
 * `id_item_cambio#id_usuario#sfsv#aviso_tipo` recibida en `sel[]`.
 */

use src\cambios\application\CambioUsuarioEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'sel' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel'),
];

/** @var CambioUsuarioEliminar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioEliminar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
