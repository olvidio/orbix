<?php

use src\ubis\application\TrasladarUbis;
use frontend\shared\web\ContestarJson;

$errorTxt = TrasladarUbis::execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
