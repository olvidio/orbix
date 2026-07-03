<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint JSON: listado de propiedades configurables del objeto indicado,
 * preseleccionadas segun las preferencias ya guardadas.
 */

use src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'objeto' => FuncTablasSupport::inputString($_POST, 'objeto'),
    'id_item_usuario_objeto' => FuncTablasSupport::inputInt($_POST, 'id_item_usuario_objeto'),
];

/** @var CambioUsuarioObjetoPrefPropiedadesData $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefPropiedadesData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
