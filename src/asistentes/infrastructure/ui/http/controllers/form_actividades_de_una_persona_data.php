<?php

use src\asistentes\application\FormActividadesDeUnaPersonaData;
use frontend\shared\web\ContestarJson;

$data = FormActividadesDeUnaPersonaData::build($_POST);
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], 'none');
    return;
}
ContestarJson::enviar('', $data);
