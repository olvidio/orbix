<?php

use src\actividadestudios\application\AsistenteObservEst;
use web\ContestarJson;

$error_txt = AsistenteObservEst::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
