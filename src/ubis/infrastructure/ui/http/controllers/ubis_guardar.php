<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisGuardar;
use src\shared\web\ContestarJson;

/** @var UbisGuardar $useCase */
$useCase = DependencyResolver::get(UbisGuardar::class);
try {
    $errorTxt = $useCase->execute($_POST);
} catch (\Throwable $e) {
    $errorTxt = $e->getMessage();
}
ContestarJson::enviar($errorTxt, 'ok');
