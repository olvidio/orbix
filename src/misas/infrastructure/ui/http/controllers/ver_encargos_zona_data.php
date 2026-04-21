<?php

use src\misas\application\VerEncargosZonaData;
use web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
if ($Qorden === '') {
    $Qorden = 'orden';
}

ContestarJson::enviar('', VerEncargosZonaData::getData($Qid_zona, $Qorden));
