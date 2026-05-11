<?php
/**
 * Endpoint backend: datos para el listado de avisos de un usuario.
 * Consumido por `frontend/cambios/controller/usuario_form_avisos.php`.
 */

use src\cambios\application\UsuarioFormAvisosData;
use src\shared\web\ContestarJson;

$input = [
    'id_usuario' => (int)filter_input(INPUT_POST, 'id_usuario'),
    'quien' => (string)filter_input(INPUT_POST, 'quien'),
];
$result = UsuarioFormAvisosData::execute($input);

$error = $result['error'];
$data = [
    'a_valores' => $result['a_valores'],
    'nombre_usuario' => $result['nombre_usuario'],
];
ContestarJson::enviar($error, $data);
