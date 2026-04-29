<?php

use src\asistentes\application\FormAsistentesAUnaActividadData;
use frontend\shared\web\ContestarJson;

$data = FormAsistentesAUnaActividadData::build($_POST);
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], 'none');
    return;
}
ContestarJson::enviar('', $data);
