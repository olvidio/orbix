<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$id_usuario = UsuariosPostInput::sessionAuthInt('id_usuario');
$usuario = UsuariosPostInput::sessionAuthString('username');
$email = UsuariosPostInput::sessionAuthString('mail');

if ($usuario === '' && $id_usuario > 0) {
    $data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_info', ['id_usuario' => $id_usuario]));
    $usuario = PayloadCoercion::string($data['usuario'] ?? '');
    $email = PayloadCoercion::string($data['email'] ?? '');
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
