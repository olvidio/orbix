<?php


/**
 * Endpoint backend: devuelve sacd candidatos para asignar a una actividad.
 */

use src\actividadessacd\application\SacdsDisponiblesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'seleccion' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'seleccion'),
];

/** @var SacdsDisponiblesData $useCase */
$useCase = DependencyResolver::get(SacdsDisponiblesData::class);
ContestarJson::enviar('', $useCase->execute($input));
