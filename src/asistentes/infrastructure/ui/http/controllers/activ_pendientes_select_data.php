<?php

use src\asistentes\application\ActivPendientesSelectData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = $GLOBALS['container']->get(ActivPendientesSelectData::class)->build($_POST);
ContestarJson::enviar('', $data);
