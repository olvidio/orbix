<?php

use src\ubis\application\TelecoTablaData;
use web\ContestarJson;

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$jsondata = ContestarJson::respuestaPhp('', TelecoTablaData::execute($Qobj_pau, $Qid_ubi));
ContestarJson::send($jsondata);
