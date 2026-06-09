<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

/**
 * Formulario para cambiar el mail por parte del usuario.
 */

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario = (int)($_SESSION['session_auth']['id_usuario'] ?? 0);

$usuario = (string)($_SESSION['session_auth']['username'] ?? '');
$email = (string)($_SESSION['session_auth']['mail'] ?? '');
if ($usuario === '' && $id_usuario > 0) {
    $data = PostRequest::getDataFromUrl('/src/usuarios/usuario_info', ['id_usuario' => $id_usuario]);
    $usuario = (string)($data['usuario'] ?? '');
    $email = (string)($data['email'] ?? '');
}

$oHash = new HashFront();
$oHash->setCamposForm('email');
$a_camposHidden = array(
    'id_usuario' => $id_usuario,
    'quien' => 'usuario',
);
$oHash->setArraycamposHidden($a_camposHidden);


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