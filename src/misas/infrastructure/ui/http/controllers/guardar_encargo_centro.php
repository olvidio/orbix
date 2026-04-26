<?php

use src\misas\application\GuardarEncargoCentro;
use frontend\shared\web\ContestarJson;

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');
$Qid_enc = (int)filter_input(INPUT_POST, 'id_enc');
$Qid_ctr = (int)filter_input(INPUT_POST, 'id_ctr');

$error = GuardarEncargoCentro::execute($Qid_item, $Qid_enc, $Qid_ctr);

ContestarJson::enviar($error, [
    'id_item' => $Qid_item,
    'id_enc' => $Qid_enc,
    'id_ctr' => $Qid_ctr,
]);
