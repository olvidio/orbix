<?php

use src\asistentes\application\ListaActivCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaActivCtrData $useCase */
$useCase = DependencyResolver::get(ListaActivCtrData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
