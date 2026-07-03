<?php


/**
 * Endpoint backend: datos de la pantalla `prevision_asistentes`.
 */

use src\casas\application\PrevisionAsistentesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'mi_of' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'mi_of'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'inicio_iso' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inicio_iso'),
    'fin_iso' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'fin_iso'),
];

/** @var PrevisionAsistentesData $useCase */
$useCase = DependencyResolver::get(PrevisionAsistentesData::class);
ContestarJson::enviar('', $useCase->execute($input));
