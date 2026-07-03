<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_usuario = (integer)FilterPostGet::post('id_usuario');

$error_txt = '';
$data = [];
if (empty($Qid_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    // grupos:
    $GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
    $UsuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
    $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => $Qid_usuario));
    $i = 0;
    $txt = '';
    foreach ($cGrupos as $oUsuarioGrupo) {
        $i++;
        $id_grupo = $oUsuarioGrupo->getId_grupo();
        $oGrupo = $GrupoRepository->findById($id_grupo);
        if ($oGrupo === null) {
            continue;
        }
        if ($i > 1) {
            $txt .= ", ";
        }
        $txt .= $oGrupo->getUsuarioVo()->value();
    }

    // datos personales usuario
    $UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    if ($oUsuario === null) {
        $error_txt = _("Usuario no encontrado");
    } else {
    $usuario = $oUsuario->getUsuarioAsString();
    $email = $oUsuario->getEmailAsString();

    $data['grupos_txt'] = $txt;
    $data['usuario'] = $usuario;
    $data['email'] = $email;
    }
}

ContestarJson::enviar($error_txt, $data);

