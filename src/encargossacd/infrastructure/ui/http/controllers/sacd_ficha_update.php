<?php

use src\encargossacd\application\SacdFichaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdFichaUpdate $useCase */
$useCase = DependencyResolver::get(SacdFichaUpdate::class);


$resultado = $useCase->execute($_POST);

ContestarJson::enviar(
    (string)$resultado['error'],
    (string)$resultado['mensajes'],
);
