<?php


/**
 * Endpoint JSON: construye el texto de preview de la condicion y el array
 * serializado (cambio_prop) sin persistir nada.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefPreview;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item'),
    'objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'objeto'),
    'propiedad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'propiedad'),
    'operador' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'operador'),
    'valor' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'valor'),
    'valor_old' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'valor_old'),
    'valor_new' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'valor_new'),
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'id_ubi'),
];

/** @var CambioUsuarioPropiedadPrefPreview $useCase */
$useCase = DependencyResolver::get(CambioUsuarioPropiedadPrefPreview::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
