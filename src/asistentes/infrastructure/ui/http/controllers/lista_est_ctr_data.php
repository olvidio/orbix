<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\ListaEstCtrData;
use src\shared\web\ContestarJson;

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ListaEstCtrData $useCase */
$useCase = $container->get(ListaEstCtrData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
