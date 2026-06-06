<?php

use src\actividades\application\TipoActivUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivUpdate $useCase */
$useCase = DependencyResolver::get(TipoActivUpdate::class);
$mensaje = $useCase->execute($_POST);

ContestarJson::enviar('', ['mensaje' => $mensaje]);
