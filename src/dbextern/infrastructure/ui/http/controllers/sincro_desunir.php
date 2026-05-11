<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\DesunirPersonaUseCase;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;

$id_nom_listas = (int)filter_input(INPUT_POST, 'id_nom_listas');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$useCase = new DesunirPersonaUseCase($idMatchRepository);
$error_txt = $useCase($id_nom_listas, $tipo_persona);

ContestarJson::enviar($error_txt, 'ok');
