<?php

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\ContestarJson;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$error_txt = '';
$data = [];
if (empty($Qid_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    // grupos:
    $GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
    $UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
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
    $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    $usuario = $oUsuario->getUsuarioAsString();
    $email = $oUsuario->getEmailAsString();

    $data['grupos_txt'] = $txt;
    $data['usuario'] = $usuario;
    $data['email'] = $email;
}

ContestarJson::enviar($error_txt, $data);

