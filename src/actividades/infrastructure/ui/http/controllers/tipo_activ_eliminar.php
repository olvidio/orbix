<?php

use src\actividades\application\TipoActivEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivEliminar $useCase */
$useCase = DependencyResolver::get(TipoActivEliminar::class);
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
