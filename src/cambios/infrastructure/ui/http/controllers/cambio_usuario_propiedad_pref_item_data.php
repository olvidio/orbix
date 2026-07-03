<?php


/**
 * Endpoint JSON: devuelve los datos de una condicion por `id_item`
 * (si existe) y la lista de casas cuando la propiedad es `id_ubi`.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefItemData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item'),
    'objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'objeto'),
    'propiedad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'propiedad'),
];

/** @var CambioUsuarioPropiedadPrefItemData $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefItemData::class);
$result = $useCase->execute($input);
$error = isset($result['error']) && is_string($result['error']) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
