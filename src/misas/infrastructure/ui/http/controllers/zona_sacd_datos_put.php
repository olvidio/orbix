<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\ZonaSacdDatosPut;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)\src\shared\domain\helpers\FilterPostGet::post('id_sacd', FILTER_VALIDATE_INT);

/** @var ZonaSacdDatosPut $useCase */
$useCase = DependencyResolver::get(ZonaSacdDatosPut::class);
$result = $useCase->execute($Qid_zona, $Qid_sacd, [
    'propia' => (string)\src\shared\domain\helpers\FilterPostGet::post('propia'),
    'dw1' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw1'),
    'dw2' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw2'),
    'dw3' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw3'),
    'dw4' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw4'),
    'dw5' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw5'),
    'dw6' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw6'),
    'dw7' => (string)\src\shared\domain\helpers\FilterPostGet::post('dw7'),
]);

ContestarJson::enviar($result['error']);
