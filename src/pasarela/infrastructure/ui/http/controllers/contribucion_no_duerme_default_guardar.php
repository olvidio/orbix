<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeDefaultGuardar;

$default = (string)filter_post('default');

/** @var ContribucionNoDuermeDefaultGuardar $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeDefaultGuardar::class);

$error_txt = $useCase->execute($default);
ContestarJson::enviar($error_txt, 'ok');
