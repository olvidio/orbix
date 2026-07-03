<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\DesplegableSacdData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)FilterPostGet::post('id_zona');
$Qid_sacd = (int)FilterPostGet::post('id_sacd');
$Qseleccion = (int)FilterPostGet::post('seleccion');
$Qdia = (string)FilterPostGet::post('dia');

/** @var DesplegableSacdData $useCase */
$useCase = DependencyResolver::get(DesplegableSacdData::class);
$result = $useCase->getData($Qid_zona, $Qid_sacd, $Qseleccion, $Qdia);
ContestarJson::enviar('', $result);
