<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerEncargosZonaData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)FilterPostGet::post('id_zona');
$Qorden = (string)FilterPostGet::post('orden');
if ($Qorden === '') {
    $Qorden = 'orden';
}

/** @var VerEncargosZonaData $useCase */
$useCase = DependencyResolver::get(VerEncargosZonaData::class);
$result = $useCase->getData($Qid_zona, $Qorden);
ContestarJson::enviar('', $result);
