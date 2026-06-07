<?php

use src\encargossacd\application\SacdAusenciasUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdAusenciasUpdate $useCase */
$useCase = DependencyResolver::get(SacdAusenciasUpdate::class);


$resultado = $useCase->execute($_POST);

ContestarJson::enviar(
    (string)$resultado['error'],
    (string)$resultado['mensajes'],
);
