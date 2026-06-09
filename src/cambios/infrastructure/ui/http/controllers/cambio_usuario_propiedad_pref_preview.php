<?php
/**
 * Endpoint JSON: construye el texto de preview de la condicion y el array
 * serializado (cambio_prop) sin persistir nada.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefPreview;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;


$input = [
    'id_item' => input_int($_POST, 'id_item'),
    'objeto' => input_string($_POST, 'objeto'),
    'propiedad' => input_string($_POST, 'propiedad'),
    'operador' => input_string($_POST, 'operador'),
    'valor' => input_string($_POST, 'valor'),
    'valor_old' => input_string($_POST, 'valor_old'),
    'valor_new' => input_string($_POST, 'valor_new'),
    'id_ubi' => input_string_list($_POST, 'id_ubi'),
];

/** @var CambioUsuarioPropiedadPrefPreview $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefPreview::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
