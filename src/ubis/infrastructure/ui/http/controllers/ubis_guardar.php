<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisGuardar;
use src\shared\web\ContestarJson;

/** @var UbisGuardar $useCase */
$useCase = DependencyResolver::get(UbisGuardar::class);
$errorTxt = $useCase->execute($_POST);
ContestarJson::enviar($errorTxt, 'ok');
