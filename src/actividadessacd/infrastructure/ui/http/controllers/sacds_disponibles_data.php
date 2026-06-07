<?php
/**
 * Endpoint backend: devuelve sacd candidatos para asignar a una actividad.
 */

use src\actividadessacd\application\SacdsDisponiblesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'seleccion' => input_int($_POST, 'seleccion'),
];

/** @var SacdsDisponiblesData $useCase */
$useCase = DependencyResolver::get(SacdsDisponiblesData::class);
ContestarJson::enviar('', $useCase->execute($input));
