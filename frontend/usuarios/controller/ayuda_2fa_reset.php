<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

/**
 * Página de ayuda para restablecer la autenticación de dos factores (2FA).
 * Esta página proporciona instrucciones detalladas para usuarios que han perdido
 * acceso a su aplicación de autenticación y necesitan restablecer su configuración de 2FA.
 */
// Crea los objetos de uso global **********************************************
//require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

require __DIR__ . '/../../../libs/vendor/autoload.php';

// vengo por $GET
$input = array_merge($_GET, $_POST);

$Qusername = filter_var($input['username'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$Qubicacion  = filter_var($input['ubicacion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$Qesquema = filter_var($input['esquema'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$Qurl_index = filter_var($input['url_index'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

$a_cosas = ['url_index' => $Qurl_index, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMail2fa = 'recuperar_2fa.php?' . http_build_query($a_cosas);

$url = str_replace('index.php', '', $Qurl_index);
$url_lista_backend = Hash::cmdSinParametros($url . 'src/usuarios/infrastructure/controllers/usuario_ayuda_info.php');

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'username' => $Qusername,
    'esquema' => $Qesquema,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$errores = $data['errores'];
$emailOfuscado = $data['emailOfuscado'];


$a_campos = [
    'error_txt' => $errores,
    'linkEnviarMail2fa' => $linkEnviarMail2fa,
    'emailOfuscado' => $emailOfuscado,
    'url_index' => $Qurl_index,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_2fa_reset.phtml', $a_campos);
