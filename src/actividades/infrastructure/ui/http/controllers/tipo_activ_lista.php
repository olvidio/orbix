<?php

use src\actividades\application\TipoActivLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivLista $useCase */
$useCase = DependencyResolver::get(TipoActivLista::class);
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
