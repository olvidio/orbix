<?php

use src\asistentes\application\FormAsistentesAUnaActividadData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(FormAsistentesAUnaActividadData::class)->build($_POST);
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], 'none');
    return;
}
ContestarJson::enviar('', $data);
