<?php

use src\asistentes\application\ListaEstCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaEstCtrData $useCase */
$useCase = DependencyResolver::get(ListaEstCtrData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
