<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

// vengo por $GET
$_POST = empty($_POST) ? $_GET : $_POST;

/**
 * Página de ayuda para restablecer la autenticación de dos factores (2FA).
 * Esta página proporciona instrucciones detalladas para usuarios que han perdido
 * acceso a su aplicación de autenticación y necesitan restablecer su configuración de 2FA.
 */
// Crea los objetos de uso global **********************************************
//require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

require __DIR__ . '/../../../libs/vendor/autoload.php';

$Qusername = (string)$_POST['username'];
$Qubicacion = (string)$_POST['ubicacion'];
$Qesquema = (string)$_POST['esquema'];
$Qurl_base = (string)$_POST['url_base'];

$a_cosas = ['url_base' => $Qurl_base, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMail2fa = 'recuperar_2fa.php?' . http_build_query($a_cosas);

$url_backend = $Qurl_base . 'src/usuarios/usuario_ayuda_info';
$a_campos_backend = [
    'username' => $Qusername,
    'esquema' => $Qesquema,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$errores = $data['errores'];
$emailOfuscado = $data['emailOfuscado'];


$a_campos = [
    'error_txt' => $errores,
    'linkEnviarMail2fa' => $linkEnviarMail2fa,
    'emailOfuscado' => $emailOfuscado,
    'url_base' => $Qurl_base,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_2fa_reset.phtml', $a_campos);
