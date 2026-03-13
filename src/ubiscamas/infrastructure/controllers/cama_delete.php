<?php
// INICIO Cabecera global de URL de controlador *********************************
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_cama = (string)filter_input(INPUT_POST, 'id_cama');

$CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);

try {
    if (!empty($Qid_cama)) {
        $oCama = $CamaRepository->findById($Qid_cama);
        if (!empty($oCama)) {
            if ($CamaRepository->Eliminar($oCama) === false) {
                echo _("hay un error, no se ha eliminado");
                echo "\n" . $CamaRepository->getErrorTxt();
            }
        } else {
            echo _("No se encontró la cama a eliminar");
        }
    } else {
        echo _("ID de cama no proporcionado");
    }
} catch (Exception $e) {
    echo _("Error al eliminar la cama") . ": " . $e->getMessage();
}
