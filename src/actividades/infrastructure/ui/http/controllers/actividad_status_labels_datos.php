<?php
/**
 * Etiquetas de status ({@see StatusId::getArrayStatus}) para el formulario actividad.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ActividadStatusLabelsDatos;

$withAll = filter_input(INPUT_POST, 'with_all') === 't';
$data = DependencyResolver::get(ActividadStatusLabelsDatos::class)->execute($withAll);

ContestarJson::enviar('', $data);
