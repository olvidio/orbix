<?php

use src\asistentes\application\FormAsistentesAUnaActividadData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FormAsistentesAUnaActividadData $useCase */
$useCase = DependencyResolver::get(FormAsistentesAUnaActividadData::class);
$data = $useCase->build($_POST);
if (isset($data['error'])) {
    $error = is_string($data['error']) ? $data['error'] : '';
    ContestarJson::enviar($error, 'none');
    return;
}
ContestarJson::enviar('', $data);
