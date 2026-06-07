<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\VerEncargosCentrosData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

/** @var VerEncargosCentrosData $useCase */
$useCase = DependencyResolver::get(VerEncargosCentrosData::class);
$result = $useCase->getData($Qid_zona);
ContestarJson::enviar('', $result);
