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

$input = [
    'id_usuario' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_usuario'),
    'aviso_tipo' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'aviso_tipo'),
    'is_admin' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'is_admin') === 1,
];

/** @var AvisosGenerarListaData $useCase */
$useCase = DependencyResolver::get(AvisosGenerarListaData::class);
$result = $useCase->execute($input);

$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);
ContestarJson::enviar($error, $result);
