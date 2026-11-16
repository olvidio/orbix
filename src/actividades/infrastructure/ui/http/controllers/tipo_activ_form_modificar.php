<?php

use src\actividades\application\TipoActivFormModificar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivFormModificar $useCase */
$useCase = DependencyResolver::get(TipoActivFormModificar::class);
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
