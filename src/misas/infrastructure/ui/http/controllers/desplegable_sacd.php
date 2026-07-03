<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\DesplegableSacdData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona');
$Qid_sacd = (int)\src\shared\domain\helpers\FilterPostGet::post('id_sacd');
$Qseleccion = (int)\src\shared\domain\helpers\FilterPostGet::post('seleccion');
$Qdia = (string)\src\shared\domain\helpers\FilterPostGet::post('dia');

/** @var DesplegableSacdData $useCase */
$useCase = DependencyResolver::get(DesplegableSacdData::class);
$result = $useCase->getData($Qid_zona, $Qid_sacd, $Qseleccion, $Qdia);
ContestarJson::enviar('', $result);
