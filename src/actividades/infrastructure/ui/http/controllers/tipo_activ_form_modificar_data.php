<?php

use src\actividades\application\TipoActivFormModificarData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivFormModificarData $useCase */
$useCase = DependencyResolver::get(TipoActivFormModificarData::class);
$data = $useCase->execute($_POST);

ContestarJson::enviar((string) ($data['error'] ?? ''), isset($data['error']) ? 'none' : $data);
