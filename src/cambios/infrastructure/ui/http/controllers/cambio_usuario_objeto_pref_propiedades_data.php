<?php
/**
 * Endpoint JSON: listado de propiedades configurables del objeto indicado,
 * preseleccionadas segun las preferencias ya guardadas.
 */

use src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;


$input = [
    'objeto' => input_string($_POST, 'objeto'),
    'id_item_usuario_objeto' => input_int($_POST, 'id_item_usuario_objeto'),
];

/** @var CambioUsuarioObjetoPrefPropiedadesData $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefPropiedadesData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
