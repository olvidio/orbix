<?php

use src\encargossacd\application\EncargoVerEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoVerEditar $useCase */
$useCase = DependencyResolver::get(EncargoVerEditar::class);


$input = $_POST;
$result = $useCase->execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
