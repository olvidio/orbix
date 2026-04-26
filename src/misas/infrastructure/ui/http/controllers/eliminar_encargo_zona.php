<?php

use src\misas\application\EliminarEncargoZona;
use frontend\shared\web\ContestarJson;

$Qid_enc = (int)filter_input(INPUT_POST, 'id_enc');

$error = EliminarEncargoZona::execute($Qid_enc);

ContestarJson::enviar($error, ['id_enc' => $Qid_enc]);
