<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\DesplegableSacdData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');
$Qseleccion = (int)filter_input(INPUT_POST, 'seleccion');
$Qdia = (string)filter_input(INPUT_POST, 'dia');

/** @var DesplegableSacdData $useCase */
$useCase = DependencyResolver::get(DesplegableSacdData::class);
$result = $useCase->getData($Qid_zona, $Qid_sacd, $Qseleccion, $Qdia);
ContestarJson::enviar('', $result);
