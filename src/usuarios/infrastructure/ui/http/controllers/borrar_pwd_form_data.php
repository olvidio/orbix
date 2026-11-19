<?php
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

// Administración de usuarios, grupos y permisos: solo los roles de administración.
if (!\src\usuarios\infrastructure\ui\http\PermisoAdministrarUsuariosGuard::permite()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

// Sin identidad de registro que atar: la mutación opera sobre todos los
// usuarios de todos los esquemas de pruebas. El ctx solo prueba que la
// petición viene de esta pantalla (mismo patrón que cabecera_pie_txt_guardar
// / activacion_default_guardar).
$data = ['ctx_guardar' => HashB::sign('borrar_pwd')];

ContestarJson::enviar('', $data);
