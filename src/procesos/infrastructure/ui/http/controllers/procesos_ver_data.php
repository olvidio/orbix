<?php

use src\procesos\application\ProcesosVerData;
use frontend\shared\web\ContestarJson;

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

ContestarJson::enviar('', ProcesosVerData::execute($Qmod, $Qid_item));
