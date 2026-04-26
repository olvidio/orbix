<?php

use src\dossiers\application\DossiersVerPantallaData;
use frontend\shared\web\ContestarJson;

$result = DossiersVerPantallaData::build($_POST);
$error = (string)($result['error'] ?? '');
unset($result['error']);

ContestarJson::enviar($error, $result);
