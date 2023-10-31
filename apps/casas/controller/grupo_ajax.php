<?php

use casas\model\entity\GrupoCasa;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case "update":
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_ubi_padre = (integer)filter_input(INPUT_POST, 'id_ubi_padre');
        $Qid_ubi_hijo = (integer)filter_input(INPUT_POST, 'id_ubi_hijo');

        $oGrupoCasa = new GrupoCasa($Qid_item);
        $oGrupoCasa->setId_ubi_padre($Qid_ubi_padre);
        $oGrupoCasa->setId_ubi_hijo($Qid_ubi_hijo);
        if ($oGrupoCasa->DBGuardar() === false) {
            echo _("Hay un error, no se ha guardado.");
        }
        break;
    case "eliminar":
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $Qid_item = urldecode(strtok($a_sel[0], "#"));

        $oGrupoCasa = new GrupoCasa($Qid_item);
        if ($oGrupoCasa->DBEliminar() === false) {
            echo _("Hay un error, no se ha eliminado.");
        }

        break;
}