<?php

use src\procesos\application\ActividadProcesoData;
use frontend\shared\web\ContestarJson;

$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');

ContestarJson::enviar('', ActividadProcesoData::execute($Qid_activ));
