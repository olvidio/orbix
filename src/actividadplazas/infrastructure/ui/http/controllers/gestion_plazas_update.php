<?php


/**
 * Endpoint backend: actualiza las plazas (totales, concedidas o
 * pedidas) desde la edicion inline de `frontend\shared\web\TablaEditable`. Responde
 * JSON `{success, mensaje, data}` via `src\shared\web\ContestarJson::enviar`
 * (contrato estandar del resto de endpoints de `src/`).
 */

use src\actividadplazas\application\GestionPlazasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'data' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'data'),
    'colName' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'colName'),
];

/** @var GestionPlazasUpdate $useCase */
$useCase = DependencyResolver::get(GestionPlazasUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
