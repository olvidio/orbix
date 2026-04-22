<?php

use src\notas\application\ActaPdfEliminar;
use web\ContestarJson;

$error_txt = ActaPdfEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
