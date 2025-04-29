<?php

use src\inventario\domain\repositories\WhereisRepository;
use src\inventario\domain\entity\Whereis;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item_egm = (integer)filter_input(INPUT_POST, 'id_item_egm');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

// grabar el pack (equipaje-grupo-lugar) + grabar los docs seleccionados
$WhereisRepository = new WhereisRepository();
foreach ($a_sel as $id_item_whereis) {
    $oWhereis = new Whereis();
    $oWhereis->setId_item_whereis($id_item_whereis);

    if ($WhereisRepository->Eliminar($oWhereis) === FALSE) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $WhereisRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');

