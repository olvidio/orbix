<?php

use src\encargossacd\application\EncargoHorarioUpdate;
use web\ContestarJson;

$result = EncargoHorarioUpdate::ejecutar($_POST);
if (isset($result['_error'])) {
    ContestarJson::enviar($result['_error'], []);
    return;
}

ContestarJson::enviar('', ['ok' => true]);
