<?php

use src\dbextern\application\VerOrbixData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$region = input_string($_POST, 'region');
$dl = input_string($_POST, 'dl');
$tipo_persona = input_string($_POST, 'tipo_persona');
$id_nom_orbix = input_int($_POST, 'id_nom_orbix');

$useCase = DependencyResolver::get(VerOrbixData::class);

if ($id_nom_orbix > 0) {
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_orbix);
} else {
    $data = $useCase($region, $tipo_persona);
}

ContestarJson::enviar('', $data);
