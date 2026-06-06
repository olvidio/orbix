<?php
/**
 * Endpoint JSON: devuelve los datos de una condicion por `id_item`
 * (si existe) y la lista de casas cuando la propiedad es `id_ubi`.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefItemData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'id_item' => input_int($_POST, 'id_item'),
    'objeto' => input_string($_POST, 'objeto'),
    'propiedad' => input_string($_POST, 'propiedad'),
];

/** @var CambioUsuarioPropiedadPrefItemData $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefItemData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
