<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: devuelve sacd candidatos para asignar a una actividad.
 */

use src\actividadessacd\application\SacdsDisponiblesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'seleccion' => FuncTablasSupport::inputInt($_POST, 'seleccion'),
];

/** @var SacdsDisponiblesData $useCase */
$useCase = DependencyResolver::get(SacdsDisponiblesData::class);
ContestarJson::enviar('', $useCase->execute($input));
