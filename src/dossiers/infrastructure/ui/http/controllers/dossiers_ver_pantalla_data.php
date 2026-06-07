<?php

use src\dossiers\application\DossiersVerPantallaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DossiersVerPantallaData $useCase */
$useCase = DependencyResolver::get(DossiersVerPantallaData::class);
$result = $useCase->build($_POST);
$error = is_string($result['error'] ?? null) ? $result['error'] : '';
unset($result['error']);

ContestarJson::enviar($error, $result);
