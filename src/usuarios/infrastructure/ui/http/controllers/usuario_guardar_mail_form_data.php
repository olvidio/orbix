<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

// Pantalla de autoservicio: siempre el propio usuario autenticado, nunca uno
// arbitrario (a diferencia de usuario_info, que sí acepta cualquier id_usuario).
$id_usuario = ConfigGlobal::mi_id_usuario();

$error_txt = '';
$data = ['email' => '', 'usuario' => ''];
if (empty($id_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    $UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioRepository->findById($id_usuario);
    if ($oUsuario === null) {
        $error_txt = _("Usuario no encontrado");
    } else {
        $data['usuario'] = $oUsuario->getUsuarioAsString();
        $data['email'] = $oUsuario->getEmailAsString();
        $data['ctx_guardar'] = HashB::sign('usuario_guardar_mail', ['id_usuario' => $id_usuario]);
    }
}

ContestarJson::enviar($error_txt, $data);
