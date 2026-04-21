<?php

use src\encargossacd\application\EncargoVerEliminar;
use web\ContestarJson;

$input = $_POST;
$result = EncargoVerEliminar::execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
