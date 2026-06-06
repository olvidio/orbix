<?php

use src\asistentes\application\ListaUltimaActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaUltimaActivData $useCase */
$useCase = DependencyResolver::get(ListaUltimaActivData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
