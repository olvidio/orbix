<?php

use src\ubis\application\TrasladarUbis;
use src\shared\web\ContestarJson;

$errorTxt = TrasladarUbis::execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
