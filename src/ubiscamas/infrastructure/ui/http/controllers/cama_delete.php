<?php

use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\CamaId;
use frontend\shared\web\ContestarJson;

$Qid_cama = (string)filter_input(INPUT_POST, 'id_cama');

$CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);

$error_txt = '';
try {
    if (!empty($Qid_cama)) {
        $uuid_cama = CamaId::fromNullableString($Qid_cama);
        $oCama = $CamaRepository->findById($uuid_cama);
        if (!empty($oCama)) {
            if ($CamaRepository->Eliminar($oCama) === false) {
                $error_txt = _("hay un error, no se ha eliminado la cama");
                $error_txt .= "\n" . $CamaRepository->getErrorTxt();
            }
        } else {
            $error_txt = _("No se encontró la cama a eliminar");
        }
    } else {
        $error_txt = _("ID de cama no proporcionado");
    }
} catch (Exception $e) {
    $error_txt = _("Error al eliminar la cama") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');