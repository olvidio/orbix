<?php

use src\ubis\application\UbisEliminar;
use web\ContestarJson;

$service = new UbisEliminar();
$errorTxt = $service->execute(
    (string)filter_input(INPUT_POST, 'obj_pau'),
    (int)filter_input(INPUT_POST, 'id_ubi')
);
ContestarJson::send(ContestarJson::respuestaPhp($errorTxt, 'ok'));
