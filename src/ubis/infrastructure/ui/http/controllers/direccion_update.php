<?php

use src\ubis\application\DireccionUpdate;
use frontend\shared\web\ContestarJson;

$errorTxt = DireccionUpdate::execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
