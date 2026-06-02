<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\ActivPendientesSelectData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ActivPendientesSelectData $useCase */
$useCase = $container->get(ActivPendientesSelectData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
