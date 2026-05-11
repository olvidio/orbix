<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\CrearTodosDesdeListasUseCase;
use src\dbextern\application\CrearPersonaDesdeListasUseCase;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

$region = (string)filter_input(INPUT_POST, 'region');
$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

$personaBDURepository = $GLOBALS['container']->get(PersonaBDURepositoryInterface::class);
$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);

$crearPersona = new CrearPersonaDesdeListasUseCase($personaBDURepository, $idMatchRepository);
$useCase = new CrearTodosDesdeListasUseCase($idMatchRepository, $crearPersona);
$result = $useCase($region, $dl, $tipo_persona);

$error_txt = !empty($result['errors']) ? implode("\n", $result['errors']) : '';
$data = ['count' => $result['count']];

ContestarJson::enviar($error_txt, $data);
