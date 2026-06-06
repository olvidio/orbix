<?php
/**
 * Endpoint backend: datos para el listado de avisos de un usuario.
 * Consumido por `frontend/cambios/controller/usuario_form_avisos.php`.
 */

use src\cambios\application\UsuarioFormAvisosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_usuario' => input_int($_POST, 'id_usuario'),
    'quien' => input_string($_POST, 'quien'),
];

/** @var UsuarioFormAvisosData $useCase */
$useCase = DependencyResolver::get(UsuarioFormAvisosData::class);
$result = $useCase->execute($input);

$error = $result['error'];
$data = [
    'a_valores' => $result['a_valores'],
    'nombre_usuario' => $result['nombre_usuario'],
];
ContestarJson::enviar($error, $data);
