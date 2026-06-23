<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionDefaultGuardar;

$default = (string)filter_post('default');

/** @var ActivacionDefaultGuardar $useCase */
$useCase = DependencyResolver::get(ActivacionDefaultGuardar::class);

$error_txt = $useCase->execute($default);
ContestarJson::enviar($error_txt, 'ok');
