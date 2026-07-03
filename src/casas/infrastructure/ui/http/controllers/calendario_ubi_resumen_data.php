<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: estudio económico de una casa (`calendario_ubi_resumen_data`).
 */

use src\casas\application\CalendarioUbiResumenData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_ubi' => FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'seccion' => FuncTablasSupport::inputString($_POST, 'seccion'),
    'G' => FuncTablasSupport::inputInt($_POST, 'G'),
    'inc_t' => FuncTablasSupport::inputInt($_POST, 'inc_t'),
];

/** @var CalendarioUbiResumenData $useCase */
$useCase = DependencyResolver::get(CalendarioUbiResumenData::class);
ContestarJson::enviar('', $useCase->execute($input));
