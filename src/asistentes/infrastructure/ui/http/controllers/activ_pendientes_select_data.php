<?php

use src\asistentes\application\ActivPendientesSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ActivPendientesSelectData $useCase */
$useCase = DependencyResolver::get(ActivPendientesSelectData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
