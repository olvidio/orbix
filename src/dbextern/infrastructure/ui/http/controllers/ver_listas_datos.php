<?php

use src\dbextern\application\VerListasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;
$region = FuncTablasSupport::inputString($_POST, 'region');
$dl = FuncTablasSupport::inputString($_POST, 'dl');
$tipo_persona = FuncTablasSupport::inputString($_POST, 'tipo_persona');
$first_load = (bool)FilterPostGet::post('first_load');
$id_nom_bdu = FuncTablasSupport::inputInt($_POST, 'id_nom_bdu');

$useCase = DependencyResolver::get(VerListasData::class);

if ($id_nom_bdu > 0) {
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_bdu);
} else {
    $data = $useCase($region, $dl, $tipo_persona, $first_load);
}

ContestarJson::enviar('', $data);
