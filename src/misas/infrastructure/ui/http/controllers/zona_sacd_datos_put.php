<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\ZonaSacdDatosPut;
use src\shared\web\ContestarJson;

$Qid_zona = (int)FilterPostGet::post('id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)FilterPostGet::post('id_sacd', FILTER_VALIDATE_INT);

/** @var ZonaSacdDatosPut $useCase */
$useCase = DependencyResolver::get(ZonaSacdDatosPut::class);
$result = $useCase->execute($Qid_zona, $Qid_sacd, [
    'propia' => (string)FilterPostGet::post('propia'),
    'dw1' => (string)FilterPostGet::post('dw1'),
    'dw2' => (string)FilterPostGet::post('dw2'),
    'dw3' => (string)FilterPostGet::post('dw3'),
    'dw4' => (string)FilterPostGet::post('dw4'),
    'dw5' => (string)FilterPostGet::post('dw5'),
    'dw6' => (string)FilterPostGet::post('dw6'),
    'dw7' => (string)FilterPostGet::post('dw7'),
]);

ContestarJson::enviar($result['error']);
