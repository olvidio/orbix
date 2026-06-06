<?php
/**
 * Endpoint backend: datos para el formulario de ingreso de una
 * actividad (`casa_ingreso_form`).
 */

use src\casas\application\CasaIngresoFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
];

/** @var CasaIngresoFormData $useCase */
$useCase = DependencyResolver::get(CasaIngresoFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
