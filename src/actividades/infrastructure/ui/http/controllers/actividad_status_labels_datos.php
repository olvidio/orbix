<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Etiquetas de status ({@see StatusId::getArrayStatus}) para el formulario actividad.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ActividadStatusLabelsDatos;

$withAll = FilterPostGet::post('with_all') === 't';
$data = DependencyResolver::get(ActividadStatusLabelsDatos::class)->execute($withAll);

ContestarJson::enviar('', $data);
