<?php

use core\ConfigGlobal;
use src\usuarios\application\repositories\UsuarioGrupoRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$sfsv = ConfigGlobal::mi_sfsv();
$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');

// elimino el grupo de permisos al usuario.
$UsuarioGrupoRepository = new UsuarioGrupoRepository();
$oUsuarioGrupo = $UsuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => $Qid_usuario, 'id_grupo' => $Qid_grupo]);

if (($oUsuarioGrupo !== null) && $UsuarioGrupoRepository->Eliminar($oUsuarioGrupo) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');