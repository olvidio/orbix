<?php

use src\encargossacd\application\EncargoVerNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoVerNuevo $useCase */
$useCase = DependencyResolver::get(EncargoVerNuevo::class);


$input = $_POST;
$result = $useCase->execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
