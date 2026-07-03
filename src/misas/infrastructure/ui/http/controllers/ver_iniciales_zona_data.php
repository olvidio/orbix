<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerInicialesZonaData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)FilterPostGet::post('id_zona');

/** @var VerInicialesZonaData $useCase */
$useCase = DependencyResolver::get(VerInicialesZonaData::class);
$result = $useCase->getData($Qid_zona);
ContestarJson::enviar('', $result);
