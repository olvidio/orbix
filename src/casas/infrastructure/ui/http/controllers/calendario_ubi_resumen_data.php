<?php
/**
 * Endpoint backend: estudio económico de una casa (`calendario_ubi_resumen_data`).
 */

use src\casas\application\CalendarioUbiResumenData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'seccion' => input_string($_POST, 'seccion'),
    'G' => input_int($_POST, 'G'),
    'inc_t' => input_int($_POST, 'inc_t'),
];

/** @var CalendarioUbiResumenData $useCase */
$useCase = DependencyResolver::get(CalendarioUbiResumenData::class);
ContestarJson::enviar('', $useCase->execute($input));
