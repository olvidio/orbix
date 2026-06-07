<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuariosRegionContactos;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$Qregion = input_string($_POST, 'region');

/** @var usuariosRegionContactos $useCase */
$useCase = DependencyResolver::get(usuariosRegionContactos::class);
$result = $useCase->execute($Qregion);

ContestarJson::enviar($result['error'], $result['data']);
