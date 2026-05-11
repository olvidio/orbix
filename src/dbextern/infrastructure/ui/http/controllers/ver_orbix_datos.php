<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\VerOrbixData;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;

$region = (string)filter_input(INPUT_POST, 'region');
$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$id_nom_orbix = (int)filter_input(INPUT_POST, 'id_nom_orbix');

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$useCase = new VerOrbixData($idMatchRepository);

if ($id_nom_orbix > 0) {
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_orbix);
} else {
    $data = $useCase($region, $tipo_persona);
}

ContestarJson::enviar('', $data);
