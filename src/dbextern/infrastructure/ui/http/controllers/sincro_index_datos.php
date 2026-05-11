<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\SincroIndexData;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo');

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$personaBDURepository = $GLOBALS['container']->get(PersonaBDURepositoryInterface::class);

$useCase = new SincroIndexData($idMatchRepository, $personaBDURepository);
$data = $useCase($tipo_persona);

$error_txt = $data['error'] ?? '';
if (!empty($error_txt)) {
    unset($data['error']);
}

ContestarJson::enviar($error_txt, $data);
