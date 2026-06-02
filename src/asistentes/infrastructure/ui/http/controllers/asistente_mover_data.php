<?php
/**
 * JSON para {@see \src\asistentes\application\AsistenteMoverData}.
 */

use src\asistentes\application\AsistenteMoverData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = $GLOBALS['container']->get(AsistenteMoverData::class)->build($_POST);
ContestarJson::enviar('', $data);
