<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\ListaAsisConjuntoActivData;
use src\shared\web\ContestarJson;

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ListaAsisConjuntoActivData $useCase */
$useCase = $container->get(ListaAsisConjuntoActivData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
