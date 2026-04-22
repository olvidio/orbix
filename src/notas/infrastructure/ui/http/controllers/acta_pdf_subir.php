<?php

use src\notas\application\ActaPdfSubir;
use web\ContestarJson;

$error_txt = ActaPdfSubir::execute($_POST, $_FILES);
ContestarJson::enviar($error_txt, 'ok');
