<?php

use src\actividadestudios\application\AsistentePlanEstOk;
use web\ContestarJson;

$error_txt = AsistentePlanEstOk::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
