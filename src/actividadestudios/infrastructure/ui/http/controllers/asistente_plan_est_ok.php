<?php

use src\actividadestudios\application\AsistentePlanEstOk;
use frontend\shared\web\ContestarJson;

$error_txt = AsistentePlanEstOk::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
