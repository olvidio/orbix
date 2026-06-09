<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$id_usuario = usuarios_session_auth_int('id_usuario');
$usuario = usuarios_session_auth_string('username');
$email = usuarios_session_auth_string('mail');

if ($usuario === '' && $id_usuario > 0) {
    $data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/usuario_info', ['id_usuario' => $id_usuario]));
    $usuario = tessera_imprimir_string($data['usuario'] ?? '');
    $email = tessera_imprimir_string($data['email'] ?? '');
}

$oHash = new HashFront();
$oHash->setCamposForm('email');
$oHash->setArraycamposHidden([
    'id_usuario' => $id_usuario,
    'quien' => 'usuario',
]);

$txt_guardar = _("guardar datos");
$txt_ok = _("se ha cambiado el mail");

$a_campos = [
    'oPosicion' => $oPosicion,
    'usuario' => $usuario,
    'oHash' => $oHash,
    'email' => $email,
    'txt_guardar' => $txt_guardar,
    'txt_ok' => $txt_ok,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form_mail.phtml', $a_campos);
