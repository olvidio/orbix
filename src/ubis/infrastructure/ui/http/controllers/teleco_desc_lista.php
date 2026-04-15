<?php

use src\ubis\application\TelecoDescLista;
use web\ContestarJson;

$Qid_tipo_teleco = (int)filter_input(INPUT_POST, 'id_tipo_teleco');
$jsondata = ContestarJson::respuestaPhp('', TelecoDescLista::execute($Qid_tipo_teleco));
ContestarJson::send($jsondata);
