<?php

use src\notas\application\AsignaturasSearchData;
use src\shared\infrastructure\DependencyResolver;

/**
 * Autocomplete jQuery-UI. Devuelve JSON raw `[{label, value}, ...]`.
 */
header('Content-Type: application/json; charset=utf-8');
echo (DependencyResolver::get(AsignaturasSearchData::class))->execute($_POST);
