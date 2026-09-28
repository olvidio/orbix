<?php
use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

// Pantalla de autoservicio: siempre el propio usuario autenticado.
$id_usuario = ConfigGlobal::mi_id_usuario();

$error_txt = '';
$data = [];
if (empty($id_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    $data['ctx_guardar'] = HashB::sign('usuario_guardar_pwd', ['id_usuario' => $id_usuario]);
}

ContestarJson::enviar($error_txt, $data);
