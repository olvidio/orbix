<?php
/**
 * Endpoint JSON: elimina un `CambioUsuarioObjetoPref`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string_list;


$a_sel = input_string_list($_POST, 'sel');
if ($a_sel !== []) {
    strtok((string)$a_sel[0], '#');
    $id_item_usuario_objeto = (int)strtok('#');
} else {
    $id_item_usuario_objeto = input_int($_POST, 'id_item_usuario_objeto');
}

$input = ['id_item_usuario_objeto' => $id_item_usuario_objeto];

/** @var CambioUsuarioObjetoPrefEliminar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefEliminar::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];

ContestarJson::enviar($error, []);
