<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\VerListasData;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;

$region = (string)filter_input(INPUT_POST, 'region');
$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$first_load = (bool)filter_input(INPUT_POST, 'first_load');
$id_nom_bdu = (int)filter_input(INPUT_POST, 'id_nom_bdu');

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$useCase = new VerListasData($idMatchRepository);

if ($id_nom_bdu > 0) {
    // Petición de matches para una persona concreta
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_bdu);
} else {
    // Carga de la lista completa
    $data = $useCase($region, $dl, $tipo_persona, $first_load);
}

ContestarJson::enviar('', $data);
