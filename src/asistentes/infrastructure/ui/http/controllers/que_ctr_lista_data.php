<?php
/**
 * JSON para {@see \src\asistentes\application\QueCtrListaData}.
 * `hash_form_html`, `periodo_form_html` y `action` absoluta:
 * {@see \frontend\asistentes\helpers\QueCtrListaRender}.
 */

use src\asistentes\application\QueCtrListaData;
use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = QueCtrListaData::build($_POST);
ContestarJson::enviar('', $data);
