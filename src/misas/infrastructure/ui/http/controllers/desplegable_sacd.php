<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\DesplegableSacdData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_post('id_zona');
$Qid_sacd = (int)filter_post('id_sacd');
$Qseleccion = (int)filter_post('seleccion');
$Qdia = (string)filter_post('dia');

/** @var DesplegableSacdData $useCase */
$useCase = DependencyResolver::get(DesplegableSacdData::class);
$result = $useCase->getData($Qid_zona, $Qid_sacd, $Qseleccion, $Qdia);
ContestarJson::enviar('', $result);
