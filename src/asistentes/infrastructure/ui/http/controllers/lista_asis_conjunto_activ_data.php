<?php

use src\asistentes\application\ListaAsisConjuntoActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaAsisConjuntoActivData $useCase */
$useCase = DependencyResolver::get(ListaAsisConjuntoActivData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
