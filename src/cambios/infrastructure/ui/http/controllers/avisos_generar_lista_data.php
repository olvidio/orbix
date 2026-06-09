<?php
/**
 * Endpoint backend: listado de avisos `CambioUsuario` (con `avisado=false`)
 * para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla
 * `avisos_generar`.
 * {@see \frontend\cambios\helpers\AvisosGenerarListaRender} compone URLs y hash de borrado.
 */

use src\cambios\application\AvisosGenerarListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;


$input = [
    'id_usuario' => input_int($_POST, 'id_usuario'),
    'aviso_tipo' => input_int($_POST, 'aviso_tipo'),
    'is_admin' => input_int($_POST, 'is_admin') === 1,
];

/** @var AvisosGenerarListaData $useCase */
$useCase = DependencyResolver::get(AvisosGenerarListaData::class);
$result = $useCase->execute($input);

$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);
ContestarJson::enviar($error, $result);
