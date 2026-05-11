<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaDefaultGuardar;

$default = (string)filter_input(INPUT_POST, 'default');

$error_txt = ContribucionReservaDefaultGuardar::execute($default);
ContestarJson::enviar($error_txt, 'ok');
