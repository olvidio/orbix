<?php

/**
 * JSON para {@see \src\asistentes\application\AsistenteMoverData}.
 */

use src\asistentes\application\AsistenteMoverData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var AsistenteMoverData $useCase */
$useCase = DependencyResolver::get(AsistenteMoverData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
