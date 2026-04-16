<?php

use src\ubis\application\TrasladarUbis;
use web\ContestarJson;

$errorTxt = TrasladarUbis::execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
