<?php

use src\zonassacd\application\ZonaCtrAjax;
use web\ContestarJson;

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

$jsondata = ContestarJson::respuestaPhp('', ZonaCtrAjax::execute($Qque, $Qid_zona, $Qid_zona_new, $QAsel));
ContestarJson::send($jsondata);
