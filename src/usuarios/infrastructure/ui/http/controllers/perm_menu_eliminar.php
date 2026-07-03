<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';
$Qid_item = 0;

$a_sel = (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel) && is_string($a_sel[0])) { //vengo de un checkbox
    strtok($a_sel[0], "#");
    $tok = strtok("#");
    $Qid_item = is_string($tok) ? (int)$tok : 0;
}

$PermMenuRepository = DependencyResolver::get(PermMenuRepositoryInterface::class);
$oUsuarioPerm = $PermMenuRepository->findById($Qid_item);
if ($oUsuarioPerm === null) {
    $error_txt .= _("no existe el registro");
} elseif ($PermMenuRepository->Eliminar($oUsuarioPerm) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $PermMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');