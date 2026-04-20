<?php
/**
 * Endpoint backend AJAX: marca como publicadas las actividades seleccionadas.
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'publicar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) {
    $ActividadDlRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    foreach ($a_sel as $id) {
        $id_activ = (integer)strtok($id, '#');
        $oActividad = $ActividadDlRepository->findById($id_activ);
        $oActividad->setPublicado('t');
        if ($ActividadDlRepository->Guardar($oActividad) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $ActividadDlRepository->getErrorTxt();
        }
    }
}

ContestarJson::enviar($error_txt);
