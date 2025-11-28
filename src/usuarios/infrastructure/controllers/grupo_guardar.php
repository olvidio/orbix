<?php

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\value_objects\Username;
use web\ContestarJson;

$Qusuario = (string)filter_input(INPUT_POST, 'usuario');

$error_txt = '';
if (empty($Qusuario)) {
    $error_txt .= _("debe poner un nombre");
}
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
if (empty($Qid_usuario)) {
    $id_usuario_new = $GrupoRepository->getNewId();
    $oGrupo = new Grupo();
    $oGrupo->setId_usuario($id_usuario_new);
} else {
    $oGrupo = $GrupoRepository->findById($Qid_usuario);
}
$oGrupo->setUsuario(new Username($Qusuario));

if ($GrupoRepository->Guardar($oGrupo) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $GrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');