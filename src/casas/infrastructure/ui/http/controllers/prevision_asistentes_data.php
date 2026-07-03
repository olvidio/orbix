<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: datos de la pantalla `prevision_asistentes`.
 */

use src\casas\application\PrevisionAsistentesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'mi_of' => FuncTablasSupport::inputString($_POST, 'mi_of'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'inicio_iso' => FuncTablasSupport::inputString($_POST, 'inicio_iso'),
    'fin_iso' => FuncTablasSupport::inputString($_POST, 'fin_iso'),
];

/** @var PrevisionAsistentesData $useCase */
$useCase = DependencyResolver::get(PrevisionAsistentesData::class);
ContestarJson::enviar('', $useCase->execute($input));
