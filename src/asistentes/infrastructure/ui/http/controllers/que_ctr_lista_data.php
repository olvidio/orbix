<?php

/**
 * JSON para {@see \src\asistentes\application\QueCtrListaData}.
 * `hash_form_html`, `periodo_form_html` y `action` absoluta:
 * {@see \frontend\asistentes\helpers\QueCtrListaRender}.
 */

use src\asistentes\application\QueCtrListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var QueCtrListaData $useCase */
$useCase = DependencyResolver::get(QueCtrListaData::class);
$data = $useCase->build($_POST);
ContestarJson::enviar('', $data);
