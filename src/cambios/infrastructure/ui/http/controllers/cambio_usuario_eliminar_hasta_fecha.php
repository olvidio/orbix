<?php


/**
 * Endpoint backend: elimina los `CambioUsuario` con fecha <= `f_fin`.
 */

use src\cambios\application\CambioUsuarioEliminarHastaFecha;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = ['f_fin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_fin')];

/** @var CambioUsuarioEliminarHastaFecha $useCase */
$useCase = DependencyResolver::get(CambioUsuarioEliminarHastaFecha::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
