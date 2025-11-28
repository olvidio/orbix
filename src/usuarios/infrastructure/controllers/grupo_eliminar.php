<?php

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_usuario = (integer)strtok($a_sel[0], "#");
}
$Gruporepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
$oGrupo = $Gruporepository->findById($id_usuario);
if ($Gruporepository->Eliminar($oGrupo) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $Gruporepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');