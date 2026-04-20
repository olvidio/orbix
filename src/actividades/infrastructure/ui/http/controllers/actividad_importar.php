<?php
/**
 * Endpoint backend AJAX: importa las actividades seleccionadas y regenera su
 * proceso cuando la app `procesos` esta instalada.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'importar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\Importada;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) {
    $ImportadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
    foreach ($a_sel as $id) {
        $id_activ = (integer)strtok($id, '#');
        $oImportada = new Importada();
        $oImportada->setId_activ($id_activ);
        if ($ImportadaRepository->Guardar($oImportada) === false) {
            $error_txt .= _("hay un error, no se ha importado");
            $error_txt .= "\n" . $ImportadaRepository->getErrorTxt();
        }
        if (ConfigGlobal::is_app_installed('procesos')) {
            $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
            $ActividadProcesoTareaRepository->generarProceso($id_activ, ConfigGlobal::mi_sfsv(), TRUE);
        }
    }
}

ContestarJson::enviar($error_txt);
