<?php


// INICIO Cabecera global de URL de controlador *********************************
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string)filter_input(INPUT_POST, 'que');

$GrupoCasaRepository = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
switch ($Qque) {
    case "update":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_ubi_padre = (integer)filter_input(INPUT_POST, 'id_ubi_padre');
        $Qid_ubi_hijo = (integer)filter_input(INPUT_POST, 'id_ubi_hijo');

        $oGrupoCasa = $GrupoCasaRepository->findById($Qid_item);
        $oGrupoCasa->setId_ubi_padre($Qid_ubi_padre);
        $oGrupoCasa->setId_ubi_hijo($Qid_ubi_hijo);
        if ($GrupoCasaRepository->Guardar($oGrupoCasa) === false) {
            echo _("Hay un error, no se ha guardado.");
        }
        break;
    case "eliminar":
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qid_item = urldecode(strtok($a_sel[0], "#"));

        $oGrupoCasa = $GrupoCasaRepository->findById($Qid_item);
        if ($GrupoCasaRepository->Eliminar($oGrupoCasa) === false) {
            echo _("Hay un error, no se ha eliminado.");
        }

        break;
}