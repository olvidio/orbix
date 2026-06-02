<?php

use Psr\Container\ContainerInterface;
/**
 * JSON para {@see \src\asistentes\application\AsistenteMoverData}.
 */

use src\asistentes\application\AsistenteMoverData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\AsistenteMoverData $useCase */
$useCase = $container->get(AsistenteMoverData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
