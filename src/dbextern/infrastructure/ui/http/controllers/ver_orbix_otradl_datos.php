<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\VerOrbixOtraDlData;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_traslados_A = (string)filter_input(INPUT_POST, 'ids_traslados_A');

$a_ids = json_decode(urldecode($ids_traslados_A), true) ?: [];

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$personaBDURepository = $GLOBALS['container']->get(PersonaBDURepositoryInterface::class);
$useCase = new VerOrbixOtraDlData($idMatchRepository, $personaBDURepository);
$data = $useCase($tipo_persona, $a_ids);

ContestarJson::enviar('', $data);
