<?php

use src\encargossacd\application\EncargoSacdHorarioUpdate;
use src\shared\web\ContestarJson;

$result = EncargoSacdHorarioUpdate::ejecutar($_POST);
if (isset($result['_error'])) {
    ContestarJson::enviar($result['_error'], []);
    return;
}

ContestarJson::enviar('', ['ok' => true]);
