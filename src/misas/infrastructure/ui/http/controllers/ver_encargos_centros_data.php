<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\VerEncargosCentrosData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona');

/** @var VerEncargosCentrosData $useCase */
$useCase = DependencyResolver::get(VerEncargosCentrosData::class);
$result = $useCase->getData($Qid_zona);
ContestarJson::enviar('', $result);
