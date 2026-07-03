<?php

use src\dbextern\application\VerOrbixData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$region = FuncTablasSupport::inputString($_POST, 'region');
$dl = FuncTablasSupport::inputString($_POST, 'dl');
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');
$id_nom_orbix = FuncTablasSupport::inputInt($_POST, 'id_nom_orbix');

$useCase = DependencyResolver::get(VerOrbixData::class);

if ($id_nom_orbix > 0) {
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_orbix);
} else {
    $data = $useCase($region, $tipo_persona);
}

$payload = [];
foreach ($data as $key => $value) {
    $payload[(string) $key] = $value;
}
ContestarJson::enviar('', $payload);
