<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint JSON: construye el texto de preview de la condicion y el array
 * serializado (cambio_prop) sin persistir nada.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefPreview;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => FuncTablasSupport::inputInt($_POST, 'id_item'),
    'objeto' => FuncTablasSupport::inputString($_POST, 'objeto'),
    'propiedad' => FuncTablasSupport::inputString($_POST, 'propiedad'),
    'operador' => FuncTablasSupport::inputString($_POST, 'operador'),
    'valor' => FuncTablasSupport::inputString($_POST, 'valor'),
    'valor_old' => FuncTablasSupport::inputString($_POST, 'valor_old'),
    'valor_new' => FuncTablasSupport::inputString($_POST, 'valor_new'),
    'id_ubi' => FuncTablasSupport::inputStringList($_POST, 'id_ubi'),
];

/** @var CambioUsuarioPropiedadPrefPreview $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefPreview::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
