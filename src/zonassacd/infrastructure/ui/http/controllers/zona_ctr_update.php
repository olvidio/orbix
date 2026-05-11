<?php

use src\zonassacd\application\ZonaCtrUpdate;
use src\shared\web\ContestarJson;

$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

ContestarJson::enviar('', ZonaCtrUpdate::execute($Qid_zona_new, $QAsel));
