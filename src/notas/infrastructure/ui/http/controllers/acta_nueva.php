<?php

use src\notas\application\ActaNueva;
use src\shared\web\ContestarJson;

$error_txt = ActaNueva::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
