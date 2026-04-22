<?php

use src\notas\application\ExaminadoresSearchData;

/**
 * Autocomplete jQuery-UI. Devuelve JSON raw `[{label, value}, ...]`.
 * No se usa `ContestarJson` porque el plugin espera el array plano
 * directamente en el cuerpo de la respuesta.
 */
header('Content-Type: application/json; charset=utf-8');
echo ExaminadoresSearchData::execute($_POST);
