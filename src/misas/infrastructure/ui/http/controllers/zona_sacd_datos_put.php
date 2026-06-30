<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\ZonaSacdDatosPut;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_post('id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)filter_post('id_sacd', FILTER_VALIDATE_INT);

/** @var ZonaSacdDatosPut $useCase */
$useCase = DependencyResolver::get(ZonaSacdDatosPut::class);
$result = $useCase->execute($Qid_zona, $Qid_sacd, [
    'propia' => (string)filter_post('propia'),
    'dw1' => (string)filter_post('dw1'),
    'dw2' => (string)filter_post('dw2'),
    'dw3' => (string)filter_post('dw3'),
    'dw4' => (string)filter_post('dw4'),
    'dw5' => (string)filter_post('dw5'),
    'dw6' => (string)filter_post('dw6'),
    'dw7' => (string)filter_post('dw7'),
]);

ContestarJson::enviar($result['error']);
