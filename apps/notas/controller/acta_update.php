<?php

use src\notas\application\ActaEliminar;
use src\notas\application\ActaModificar;
use src\notas\application\ActaNueva;
use web\ContestarJson;

/**
 * Shim de compatibilidad para `acta_ver.phtml`.
 *
 * @deprecated Usar los endpoints granulares de `src/notas`:
 *   - `src/notas/acta_nueva`
 *   - `src/notas/acta_modificar`
 *   - `src/notas/acta_eliminar`
 *
 * La logica de negocio vive en `ActaNueva`, `ActaModificar` y
 * `ActaEliminar`. Este dispatcher se mantiene hasta que `acta_ver.phtml`
 * se migre (slice 3/4) y apunte directamente a los endpoints por
 * accion.
 */
require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$Qmod = (string)filter_input(INPUT_POST, 'mod');

switch ($Qmod) {
    case 'nueva':
        $error_txt = ActaNueva::execute($_POST);
        break;
    case 'eliminar':
        $error_txt = ActaEliminar::execute($_POST);
        break;
    case 'modificar':
    default:
        $error_txt = ActaModificar::execute($_POST);
        break;
}

ContestarJson::enviar($error_txt, 'ok');
