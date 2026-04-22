<?php

use src\notas\application\AsignaturasSearchData;

/**
 * Autocomplete jQuery-UI. Devuelve JSON raw `[{label, value}, ...]`.
 */
header('Content-Type: application/json; charset=utf-8');
echo AsignaturasSearchData::execute($_POST);
