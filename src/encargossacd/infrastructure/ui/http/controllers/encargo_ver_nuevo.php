<?php

use src\encargossacd\application\EncargoVerNuevo;
use frontend\shared\web\ContestarJson;

$input = $_POST;
$result = EncargoVerNuevo::execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
