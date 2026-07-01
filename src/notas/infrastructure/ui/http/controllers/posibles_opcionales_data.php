<?php

use src\notas\application\PosiblesOpcionalesData;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Devuelve las asignaturas opcionales que puede cursar la persona con
 * el contrato estandar de desplegable.
 */
$aOpciones = (DependencyResolver::get(PosiblesOpcionalesData::class))->execute($_POST);

$payload = [
    'id' => 'id_asignatura',
    'opciones' => OpcionesDesplegable::enOrden($aOpciones),
    'blanco' => true,
    'val_blanco' => '',
    'selected' => '',
];

ContestarJson::enviar('', $payload);
