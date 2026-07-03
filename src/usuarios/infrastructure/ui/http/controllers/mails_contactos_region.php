<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuariosRegionContactos;
use src\shared\web\ContestarJson;

$Qregion = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'region');

/** @var usuariosRegionContactos $useCase */
$useCase = DependencyResolver::get(usuariosRegionContactos::class);
$result = $useCase->execute($Qregion);

ContestarJson::enviar($result['error'], $result['data']);
