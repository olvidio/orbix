<?php

use src\notas\application\ActaPdfSubir;
use frontend\shared\web\ContestarJson;

$error_txt = ActaPdfSubir::execute($_POST, $_FILES);
ContestarJson::enviar($error_txt, 'ok');
