<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeDefaultGuardar;

$default = (string)\src\shared\domain\helpers\FilterPostGet::post('default');

/** @var ContribucionNoDuermeDefaultGuardar $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeDefaultGuardar::class);

$error_txt = $useCase->execute($default);
ContestarJson::enviar($error_txt, 'ok');
