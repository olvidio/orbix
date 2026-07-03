<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaDefaultGuardar;

$default = (string)FilterPostGet::post('default');

/** @var ContribucionReservaDefaultGuardar $useCase */
$useCase = DependencyResolver::get(ContribucionReservaDefaultGuardar::class);

$error_txt = $useCase->execute($default);
ContestarJson::enviar($error_txt, 'ok');
