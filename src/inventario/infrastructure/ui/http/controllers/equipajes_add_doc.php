<?php

use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Whereis;
use web\ContestarJson;

$Qid_item_egm = (integer)filter_input(INPUT_POST, 'id_item_egm');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

// grabar el pack (equipaje-grupo-lugar) + grabar los docs seleccionados
$WhereisRepository = $GLOBALS['container']->get(WhereisRepositoryInterface::class);
foreach ($a_sel as $id_doc) {
    $id_item_whereis = $WhereisRepository->getNewId();
    $oWhereis = new Whereis();
    $oWhereis->setId_item_whereis($id_item_whereis);
    $oWhereis->setId_item_egm($Qid_item_egm);
    $oWhereis->setId_doc($id_doc);

    if ($WhereisRepository->Guardar($oWhereis) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $WhereisRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');

