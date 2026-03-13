<?php
// INICIO Cabecera global de URL de controlador *********************************
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use web\ContestarJson;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

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
        echo _("hay un error, no se ha eliminado");
        echo "\n" . $HabitacionRepository->getErrorTxt();
    }
} catch (Exception $e) {
    $error_txt = _("Error al eliminar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
