<?php

use core\ConfigGlobal;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\entity\UsuarioGrupo;
use web\ContestarJson;

$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
// añado el grupo de permisos al usuario.
$UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
$oUsuarioGrupo = new UsuarioGrupo();
$oUsuarioGrupo->setId_usuario($Qid_usuario);
$oUsuarioGrupo->setId_grupo($Qid_grupo);
if ($UsuarioGrupoRepository->Guardar($oUsuarioGrupo) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');