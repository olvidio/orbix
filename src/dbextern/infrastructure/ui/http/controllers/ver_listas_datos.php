<?php

use src\dbextern\application\VerListasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;
$region = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'region');
$dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo_persona');
$first_load = (bool)\src\shared\domain\helpers\FilterPostGet::post('first_load');
$id_nom_bdu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom_bdu');

$useCase = DependencyResolver::get(VerListasData::class);

if ($id_nom_bdu > 0) {
    $data = $useCase->getPosiblesMatches($tipo_persona, $region, $dl, $id_nom_bdu);
} else {
    $data = $useCase($region, $dl, $tipo_persona, $first_load);
}

ContestarJson::enviar('', $data);
