<?php

use Psr\Container\ContainerInterface;
/**
 * JSON para {@see \src\asistentes\application\ListaUltimQueCtrData}.
 * `hash_form_html` y `form_action` absoluta: {@see \frontend\asistentes\helpers\ListaUltimQueCtrRender}.
 */

use src\asistentes\application\ListaUltimQueCtrData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\ListaUltimQueCtrData $useCase */
$useCase = $container->get(ListaUltimQueCtrData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
