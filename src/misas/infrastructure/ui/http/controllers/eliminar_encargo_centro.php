<?php

use src\misas\application\EliminarEncargoCentro;
use web\ContestarJson;

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

$error = EliminarEncargoCentro::execute($Qid_item);

ContestarJson::enviar($error, ['id_item' => $Qid_item]);
