<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionDefaultGuardar;

$default = (string)filter_input(INPUT_POST, 'default');

$error_txt = ActivacionDefaultGuardar::execute($default);
ContestarJson::enviar($error_txt, 'ok');
