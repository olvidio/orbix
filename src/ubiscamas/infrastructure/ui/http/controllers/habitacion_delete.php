<?php

use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use web\ContestarJson;
use function core\is_true;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qid_habitacion = (string)filter_input(INPUT_POST, 'id_habitacion');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
    $Qid_habitacion = urldecode(strtok($a_sel[0], "#"));
}

$HabitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);

$error_txt = '';
try {
    $oHabitacion = $HabitacionRepository->findById($Qid_habitacion);
    if ($HabitacionRepository->Eliminar($oHabitacion) === false) {
        $error_txt = _("hay un error, no se ha eliminado la habitación");
        $error_txt .= "\n" . $HabitacionRepository->getErrorTxt();
    }
} catch (Exception $e) {
    $error_txt = _("Error al eliminar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
