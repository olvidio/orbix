<?php
/**
 * Etiquetas de status ({@see StatusId::getArrayStatus}) para el formulario actividad.
 */

use frontend\shared\web\ContestarJson;
use src\actividades\application\ActividadStatusLabelsDatos;

$withAll = filter_input(INPUT_POST, 'with_all') === 't';
$data = (new ActividadStatusLabelsDatos())->execute($withAll);

ContestarJson::enviar('', $data);
