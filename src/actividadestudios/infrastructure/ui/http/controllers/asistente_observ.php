<?php

use src\actividadestudios\application\AsistenteObserv;
use src\shared\web\ContestarJson;

$error_txt = AsistenteObserv::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
