<?php

use src\notas\application\TesseraCopiar;
use src\shared\web\ContestarJson;

$error_txt = TesseraCopiar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
