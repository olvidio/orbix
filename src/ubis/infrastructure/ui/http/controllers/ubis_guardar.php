<?php

use src\ubis\application\UbisGuardar;
use src\shared\web\ContestarJson;

$service = new UbisGuardar();
$errorTxt = $service->execute($_POST);
ContestarJson::enviar($errorTxt, 'ok');
