<?php

use src\notas\application\PosiblesPreceptoresData;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Devuelve el desplegable de posibles preceptores (profesores STGR) con
 * el contrato estandar de refactor.md.
 */
$aOpciones = (DependencyResolver::get(PosiblesPreceptoresData::class))->execute();

$payload = [
    'id' => 'id_preceptor',
    'opciones' => OpcionesDesplegable::enOrden($aOpciones),
    'blanco' => true,
    'val_blanco' => '',
    'selected' => '',
];

ContestarJson::enviar('', $payload);
