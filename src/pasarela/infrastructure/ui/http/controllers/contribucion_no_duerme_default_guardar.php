<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeDefaultGuardar;

$default = (string)filter_input(INPUT_POST, 'default');

$error_txt = ContribucionNoDuermeDefaultGuardar::execute($default);
ContestarJson::enviar($error_txt, 'ok');
