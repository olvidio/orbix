<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\ZonaSacdDatosGet;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)\src\shared\domain\helpers\FilterPostGet::post('id_sacd', FILTER_VALIDATE_INT);

/** @var ZonaSacdDatosGet $useCase */
$useCase = DependencyResolver::get(ZonaSacdDatosGet::class);
$result = $useCase->execute($Qid_zona, $Qid_sacd);

ContestarJson::enviar($result['error'], $result['payload']);
