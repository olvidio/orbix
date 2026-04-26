<?php

use src\zonassacd\application\ZonaSacdUpdate;
use frontend\shared\web\ContestarJson;

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$Qacumular = (int)filter_input(INPUT_POST, 'acumular');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

ContestarJson::enviar('', ZonaSacdUpdate::execute($Qid_zona, $Qid_zona_new, $Qacumular, $QAsel));
