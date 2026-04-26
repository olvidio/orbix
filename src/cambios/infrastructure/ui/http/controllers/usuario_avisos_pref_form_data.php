<?php
/**
 * Endpoint JSON que devuelve la informacion necesaria para pintar el
 * formulario `usuario_avisos_pref` (edicion de un aviso de usuario/grupo).
 *
 * Sucesor del backend de `apps/cambios/controller/usuario_avisos_pref.php`.
 */

use src\cambios\application\UsuarioAvisosPrefFormData;
use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_usuario = (int)strtok((string)$a_sel[0], '#');
    $id_item_usuario_objeto = (int)strtok('#');
} else {
    $id_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
    $id_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$input = [
    'id_usuario' => $id_usuario,
    'id_item_usuario_objeto' => $id_item_usuario_objeto,
    'salida' => (string)filter_input(INPUT_POST, 'salida'),
    'quien' => (string)filter_input(INPUT_POST, 'quien'),
];

$result = UsuarioAvisosPrefFormData::execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
