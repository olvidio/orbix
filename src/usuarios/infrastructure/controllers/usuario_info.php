<?php

use src\usuarios\application\repositories\GrupoRepository;
use src\usuarios\application\repositories\UsuarioGrupoRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$error_txt = '';
$data = [];
if (empty($Qid_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    // grupos:
    $GrupoRepository = new GrupoRepository();
    $UsuarioGrupoRepository = new UsuarioGrupoRepository();
    $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
    $i = 0;
    $txt = '';
    foreach ($cGrupos as $oUsuarioGrupo) {
        $i++;
        $id_grupo = $oUsuarioGrupo->getId_grupo();
        $oGrupo = $GrupoRepository->findById($id_grupo);
        if ($i > 1) {
            $txt .= ", ";
        }
        $txt .= $oGrupo->getUsuario();
    }

    // datos personales usuario
    $UsuarioRepository = new UsuarioRepository();
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    $usuario = $oUsuario->getUsuarioAsString();
    $email = $oUsuario->getEmailAsString();

    $data['grupos_txt'] = $txt;
    $data['usuario'] = $usuario;
    $data['email'] = $email;
}

ContestarJson::enviar($error_txt, $data);

