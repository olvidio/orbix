<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\ListaAsistentesData;
use src\shared\web\ContestarJson;

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ListaAsistentesData $useCase */
$useCase = $container->get(ListaAsistentesData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
