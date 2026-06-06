<?php
/**
 * Endpoint backend: datos del formulario `GrupoCasa`.
 */

use src\casas\application\GrupoCasaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$input = [
    'id_item' => input_string($_POST, 'id_item'),
];

/** @var GrupoCasaFormData $useCase */
$useCase = DependencyResolver::get(GrupoCasaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
