<?php

use src\asistentes\application\ListaAsistentesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaAsistentesData $useCase */
$useCase = DependencyResolver::get(ListaAsistentesData::class);
$data = $useCase->build($_POST);
$error = $data['error'] ?? '';
if (is_string($error) && $error !== '') {
    ContestarJson::enviar($error, 'none');
    return;
}
ContestarJson::enviar('', $data);
