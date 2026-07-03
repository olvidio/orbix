<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuariosRegionContactos;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qregion = FuncTablasSupport::inputString($_POST, 'region');

/** @var usuariosRegionContactos $useCase */
$useCase = DependencyResolver::get(usuariosRegionContactos::class);
$result = $useCase->execute($Qregion);

ContestarJson::enviar($result['error'], $result['data']);
