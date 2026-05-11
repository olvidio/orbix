<?php

use src\ubis\application\DireccionUpdate;
use src\shared\web\ContestarJson;

$errorTxt = DireccionUpdate::execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
