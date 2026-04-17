<?php

/*
 * DEPRECADO: dispatcher multi-`que` heredado de
 * apps/procesos/controller/tipo_activ_proceso_ajax.php. Mantener solo como
 * wrapper de compatibilidad; los endpoints canonicos son:
 *   - /src/procesos/tipo_activ_proceso_lista
 *   - /src/procesos/tipo_activ_proceso_lst_posibles
 *   - /src/procesos/tipo_activ_proceso_asignar
 */

use src\procesos\application\TipoActivProcesoAsignar;
use src\procesos\application\TipoActivProcesoLista;
use src\procesos\application\TipoActivProcesoLstPosibles;

header('Content-Type: text/plain; charset=UTF-8');

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'lista':
        echo (new TipoActivProcesoLista())->execute($_POST);
        break;

    case 'lst_posibles_procesos':
        echo (new TipoActivProcesoLstPosibles())->execute($_POST);
        break;

    case 'asignar':
        echo (new TipoActivProcesoAsignar())->execute($_POST);
        break;
}
