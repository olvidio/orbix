<?php

use src\notas\application\AsignaturasSearchData;
use src\notas\application\ExaminadoresSearchData;

/**
 * Shim AJAX consumido por los autocomplete de `acta_ver.phtml`
 * (examinadores y asignaturas). El contrato de respuesta es un JSON
 * raw `[{label, value}, ...]` para jQuery-UI autocomplete, por eso no
 * se usa `ContestarJson` aqui.
 *
 * @deprecated Slice 3 lo sustituira por endpoints dedicados:
 *   - `src/notas/infrastructure/ui/http/controllers/examinadores_search.php`
 *   - `src/notas/infrastructure/ui/http/controllers/asignaturas_search.php`
 */
require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'examinadores':
        echo ExaminadoresSearchData::execute($_POST);
        break;
    case 'asignaturas':
        echo AsignaturasSearchData::execute($_POST);
        break;
}
