<?php
/**
 * JSON para {@see \src\asistentes\application\ListaUltimQueCtrData}.
 * `hash_form_html` y `form_action` absoluta: {@see \frontend\asistentes\helpers\ListaUltimQueCtrRender}.
 */

use src\asistentes\application\ListaUltimQueCtrData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = $GLOBALS['container']->get(ListaUltimQueCtrData::class)->build($_POST);
ContestarJson::enviar('', $data);
