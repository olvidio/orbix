<?php

/*
 * DEPRECADO: dispatcher multi-`que` heredado de
 * apps/procesos/controller/fases_activ_cambio_ajax.php. Mantener solo como
 * wrapper de compatibilidad; los endpoints canonicos son:
 *   - /src/procesos/fases_activ_cambio_lista
 *   - /src/procesos/fases_activ_cambio_update
 *   - /src/procesos/fases_activ_cambio_get
 */

use src\procesos\application\FasesActivCambioGet;
use src\procesos\application\FasesActivCambioLista;
use src\procesos\application\FasesActivCambioUpdate;

header('Content-Type: text/plain; charset=UTF-8');

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'lista':
        echo (new FasesActivCambioLista())->execute($_POST);
        break;

    case 'update':
        echo (new FasesActivCambioUpdate())->execute($_POST);
        break;

    case 'get':
        echo (new FasesActivCambioGet())->execute($_POST);
        break;
}
