<?php

use src\notas\application\ActaModificar;
use web\ContestarJson;

$error_txt = ActaModificar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
