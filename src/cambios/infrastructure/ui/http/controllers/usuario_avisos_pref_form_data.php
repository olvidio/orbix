<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint JSON que devuelve la informacion necesaria para pintar el
 * formulario `usuario_avisos_pref` (edicion de un aviso de usuario/grupo).
 */

use src\cambios\application\UsuarioAvisosPrefFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel = FuncTablasSupport::inputStringList($_POST, 'sel');
if ($a_sel !== []) {
    $id_usuario = (int)strtok((string)$a_sel[0], '#');
    $id_item_usuario_objeto = (int)strtok('#');
} else {
    $id_usuario = FuncTablasSupport::inputInt($_POST, 'id_usuario');
    $id_item_usuario_objeto = FuncTablasSupport::inputInt($_POST, 'id_item_usuario_objeto');
}

$input = [
    'id_usuario' => $id_usuario,
    'id_item_usuario_objeto' => $id_item_usuario_objeto,
    'salida' => FuncTablasSupport::inputString($_POST, 'salida'),
    'quien' => FuncTablasSupport::inputString($_POST, 'quien'),
];

/** @var UsuarioAvisosPrefFormData $useCase */
$useCase = DependencyResolver::get(UsuarioAvisosPrefFormData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
