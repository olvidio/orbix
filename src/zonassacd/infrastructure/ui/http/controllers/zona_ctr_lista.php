<?php

use src\zonassacd\application\ZonaCtrLista;
use web\ContestarJson;

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$jsondata = ContestarJson::respuestaPhp('', ZonaCtrLista::execute($Qid_zona));
ContestarJson::send($jsondata);
