<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: eliminar el Ingreso de una actividad.
 */

use src\casas\application\CasaIngresoEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_activ' => FuncTablasSupport::inputInt($_POST, 'id_activ'),
];

/** @var CasaIngresoEliminar $useCase */
$useCase = DependencyResolver::get(CasaIngresoEliminar::class);
$result = $useCase->execute($input);
        if ($result['ok']) {
    ContestarJson::enviar('', $result['data']);
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
