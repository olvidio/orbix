<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\ListaActivCtrData;
use src\shared\web\ContestarJson;

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ListaActivCtrData $useCase */
$useCase = $container->get(ListaActivCtrData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
