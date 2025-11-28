<?php

use core\ConfigGlobal;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use web\ContestarJson;

$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');

// elimino el grupo de permisos al usuario.
$UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
$cUsuarioGrupo = $UsuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => $Qid_usuario, 'id_grupo' => $Qid_grupo]);
if (!empty($cUsuarioGrupo)) {
    $oUsuarioGrupo = $cUsuarioGrupo[0];
    if (($oUsuarioGrupo !== null) && $UsuarioGrupoRepository->Eliminar($oUsuarioGrupo) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');