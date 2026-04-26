<?php

use src\notas\application\ActaEliminar;
use frontend\shared\web\ContestarJson;

$error_txt = ActaEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
