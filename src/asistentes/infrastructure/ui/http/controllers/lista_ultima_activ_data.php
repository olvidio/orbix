<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\ListaUltimaActivData;
use src\shared\web\ContestarJson;

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ListaUltimaActivData $useCase */
$useCase = $container->get(ListaUltimaActivData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
