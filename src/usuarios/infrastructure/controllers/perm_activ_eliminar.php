<?php

use procesos\model\entity\PermUsuarioActividad;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use web\ContestarJson;

// FIN de  Cabecera global de URL de controlador **********

$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
}
$GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
$oUsuario = $GrupoRepository->findById($Qid_usuario); // La tabla y su heredada
$oUsuarioPerm = new PermUsuarioActividad(array('id_item' => $Qid_item));
if ($oUsuarioPerm->DBEliminar() === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $oUsuarioPerm->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');