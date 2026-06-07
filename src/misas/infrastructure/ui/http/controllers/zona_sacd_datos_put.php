<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\ZonaSacdDatosPut;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona', FILTER_VALIDATE_INT);
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd', FILTER_VALIDATE_INT);

/** @var ZonaSacdDatosPut $useCase */
$useCase = DependencyResolver::get(ZonaSacdDatosPut::class);
$result = $useCase->execute($Qid_zona, $Qid_sacd, [
    'dw1' => (string)filter_input(INPUT_POST, 'dw1'),
    'dw2' => (string)filter_input(INPUT_POST, 'dw2'),
    'dw3' => (string)filter_input(INPUT_POST, 'dw3'),
    'dw4' => (string)filter_input(INPUT_POST, 'dw4'),
    'dw5' => (string)filter_input(INPUT_POST, 'dw5'),
    'dw6' => (string)filter_input(INPUT_POST, 'dw6'),
    'dw7' => (string)filter_input(INPUT_POST, 'dw7'),
]);

ContestarJson::enviar($result['error']);
