<?php

/*
 * DEPRECADO: dispatcher multi-`que` heredado de
 * apps/procesos/controller/actividad_proceso_ajax.php. Mantener solo como
 * wrapper de compatibilidad; los endpoints canonicos son:
 *   - /src/procesos/actividad_proceso_generar
 *   - /src/procesos/actividad_proceso_get
 *   - /src/procesos/actividad_proceso_update
 */

use src\procesos\application\ActividadProcesoGenerar;
use src\procesos\application\ActividadProcesoGet;
use src\procesos\application\ActividadProcesoUpdate;

header('Content-Type: text/plain; charset=UTF-8');

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'generar':
        echo (new ActividadProcesoGenerar())->execute($_POST);
        break;

    case 'get':
        echo (new ActividadProcesoGet())->execute($_POST);
        break;

    case 'update':
        echo (new ActividadProcesoUpdate())->execute($_POST);
        break;
}
