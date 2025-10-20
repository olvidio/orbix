<?php

use src\usuarios\application\repositories\GrupoRepository;
use src\usuarios\application\repositories\UsuarioGrupoRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';
$data = [];

$Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');

if (!empty($Qid_usuario)) {
    // Listado de grupos del usuario
    $GrupoRepository = new GrupoRepository();
    $UsuarioGrupoRepository = new UsuarioGrupoRepository();
    $cListaGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => $Qid_usuario]);
    $i = 0;
    $txt = '';
    foreach ($cListaGrupos as $oUsuarioGrupo) {
        $i++;
        $id_grupo = $oUsuarioGrupo->getId_grupo();
        $oGrupo = $GrupoRepository->findById($id_grupo);
        if ($i > 1) {
            $txt .= ", ";
        }
        if ($oGrupo) {
            $txt .= $oGrupo->getUsuario();
        }
    }
    $data['grupos_txt'] = $txt;
} else {
    $error_txt = _("Falta el identificador de usuario");
}

ContestarJson::enviar($error_txt, $data);