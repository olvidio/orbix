<?php

use src\encargossacd\application\EncargoVerEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoVerEliminar $useCase */
$useCase = DependencyResolver::get(EncargoVerEliminar::class);


$input = $_POST;
$result = $useCase->execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
