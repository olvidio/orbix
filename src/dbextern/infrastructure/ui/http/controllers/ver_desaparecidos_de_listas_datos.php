<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\VerDesaparecidosDeListasData;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_desaparecidos_de_listas = (string)filter_input(INPUT_POST, 'ids_desaparecidos_de_listas');

$a_ids = json_decode(urldecode($ids_desaparecidos_de_listas), true) ?: [];

$personaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
$useCase = new VerDesaparecidosDeListasData($personaDlRepository);
$data = $useCase($tipo_persona, $a_ids);

ContestarJson::enviar('', $data);
