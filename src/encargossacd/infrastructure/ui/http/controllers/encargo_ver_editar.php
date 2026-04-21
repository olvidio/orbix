<?php

use src\encargossacd\application\EncargoVerEditar;
use web\ContestarJson;

$input = $_POST;
$result = EncargoVerEditar::execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
