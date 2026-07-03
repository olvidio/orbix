<?php


/**
 * Endpoint backend: datos para el listado de avisos de un usuario.
 * Consumido por `frontend/cambios/controller/usuario_form_avisos.php`.
 */

use src\cambios\application\UsuarioFormAvisosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_usuario' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_usuario'),
    'quien' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'quien'),
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
