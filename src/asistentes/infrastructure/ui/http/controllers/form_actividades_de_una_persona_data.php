<?php

use src\asistentes\application\FormActividadesDeUnaPersonaData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(FormActividadesDeUnaPersonaData::class)->build($_POST);
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], 'none');
    return;
}
ContestarJson::enviar('', $data);
