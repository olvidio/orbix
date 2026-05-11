<?php

use src\misas\application\ZonaSacdDatosGet;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');

$result = ZonaSacdDatosGet::execute($Qid_zona, $Qid_sacd);

ContestarJson::enviar((string)($result['error'] ?? ''), $result['payload'] ?? []);
