<?php

/**
 * JSON para {@see \src\asistentes\application\ListaUltimQueCtrData}.
 * `hash_form_html` y `form_action` absoluta: {@see \frontend\asistentes\helpers\ListaUltimQueCtrRender}.
 */

use src\asistentes\application\ListaUltimQueCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ListaUltimQueCtrData $useCase */
$useCase = DependencyResolver::get(ListaUltimQueCtrData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
