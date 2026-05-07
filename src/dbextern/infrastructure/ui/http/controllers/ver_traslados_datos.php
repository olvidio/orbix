<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\VerTrasladosData;

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_traslados = (string)filter_input(INPUT_POST, 'ids_traslados');

$a_ids_traslados = json_decode(urldecode($ids_traslados), true) ?: [];

$useCase = new VerTrasladosData();
$data = $useCase($tipo_persona, $a_ids_traslados);

ContestarJson::enviar('', $data);
