<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: datos del formulario `GrupoCasa`.
 */

use src\casas\application\GrupoCasaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => FuncTablasSupport::inputString($_POST, 'id_item'),
];

/** @var GrupoCasaFormData $useCase */
$useCase = DependencyResolver::get(GrupoCasaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
