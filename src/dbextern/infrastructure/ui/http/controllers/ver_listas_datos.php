<?php

use src\dbextern\application\VerListasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$region = input_string($_POST, 'region');
$dl = input_string($_POST, 'dl');
$tipo_persona = input_string($_POST, 'tipo_persona');
$first_load = (bool)filter_input(INPUT_POST, 'first_load');
$id_nom_bdu = input_int($_POST, 'id_nom_bdu');

$useCase = DependencyResolver::get(VerListasData::class);

if ($id_nom_bdu > 0) {
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_bdu);
} else {
    $data = $useCase($region, $dl, $tipo_persona, $first_load);
}

ContestarJson::enviar('', $data);
