<?php

use src\asistentes\application\FormActividadesDeUnaPersonaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FormActividadesDeUnaPersonaData $useCase */
$useCase = DependencyResolver::get(FormActividadesDeUnaPersonaData::class);
$data = $useCase->build($_POST);
if (isset($data['error'])) {
    $error = is_string($data['error']) ? $data['error'] : '';
    ContestarJson::enviar($error, 'none');
    return;
}
ContestarJson::enviar('', $data);
