<?php

use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
}

$PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
$oUsuarioPerm = $PermMenuRepository->findById($Qid_item);
if ($PermMenuRepository->Eliminar($oUsuarioPerm) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $PermMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');