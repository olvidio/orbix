<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$a_sel = input_string_list($_POST, 'sel');

$Qid_habitacion = input_string($_POST, 'id_habitacion');

if ($a_sel !== []) {
    $Qid_habitacion = urldecode(strtok($a_sel[0], '#') ?: '');
}

/** @var HabitacionDlRepositoryInterface $habitacionRepository */
$habitacionRepository = DependencyResolver::get(HabitacionDlRepositoryInterface::class);

$error_txt = '';
try {
    $oHabitacion = $habitacionRepository->findById($Qid_habitacion);
    if ($oHabitacion === null) {
        $error_txt = _("No se encontró la habitación a eliminar");
    } elseif ($habitacionRepository->Eliminar($oHabitacion) === false) {
        $error_txt = _("hay un error, no se ha eliminado la habitación");
        $error_txt .= "\n" . $habitacionRepository->getErrorTxt();
    }
} catch (Exception $e) {
    $error_txt = _("Error al eliminar la habitación") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
