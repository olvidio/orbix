<?php

use src\actividades\application\TipoActivNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivNuevo $useCase */
$useCase = DependencyResolver::get(TipoActivNuevo::class);
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
