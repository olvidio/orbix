<?php
/**
 * Endpoint backend: datos de la pantalla `prevision_asistentes`.
 */

use src\casas\application\PrevisionAsistentesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$input = [
    'mi_of' => input_string($_POST, 'mi_of'),
    'periodo' => input_string($_POST, 'periodo'),
    'inicio_iso' => input_string($_POST, 'inicio_iso'),
    'fin_iso' => input_string($_POST, 'fin_iso'),
];

/** @var PrevisionAsistentesData $useCase */
$useCase = DependencyResolver::get(PrevisionAsistentesData::class);
ContestarJson::enviar('', $useCase->execute($input));
