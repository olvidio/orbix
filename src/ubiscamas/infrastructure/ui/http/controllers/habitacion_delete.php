<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
$a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');

$Qid_habitacion = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_habitacion');

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
