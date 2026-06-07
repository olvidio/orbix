<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerInicialesZonaData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

/** @var VerInicialesZonaData $useCase */
$useCase = DependencyResolver::get(VerInicialesZonaData::class);
$result = $useCase->getData($Qid_zona);
ContestarJson::enviar('', $result);
