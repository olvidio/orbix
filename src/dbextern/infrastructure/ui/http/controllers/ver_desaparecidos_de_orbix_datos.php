<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\VerDesaparecidosDeOrbixData;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_desaparecidos_de_orbix = (string)filter_input(INPUT_POST, 'ids_desaparecidos_de_orbix');

$a_ids = json_decode(urldecode($ids_desaparecidos_de_orbix), true) ?: [];

$personaBDURepository = $GLOBALS['container']->get(PersonaBDURepositoryInterface::class);
$useCase = new VerDesaparecidosDeOrbixData($personaBDURepository);
$data = $useCase($tipo_persona, $a_ids);

ContestarJson::enviar('', $data);
