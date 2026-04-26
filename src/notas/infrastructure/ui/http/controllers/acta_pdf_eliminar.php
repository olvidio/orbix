<?php

use src\notas\application\ActaPdfEliminar;
use frontend\shared\web\ContestarJson;

$error_txt = ActaPdfEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
