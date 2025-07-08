<?php

use core\ConfigGlobal;
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

$Qusername = (string)filter_input(INPUT_POST, 'username');
$Qubicacion = (string)filter_input(INPUT_POST, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_POST, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_POST, 'esquema_web');

if (empty($Qusername)) {
    exit (_("Debe ingresar un nombre de usuario"));
}

$url_index = $_SERVER['HTTP_REFERER'];
$a_cosas = ['url_index' => $url_index, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMailPasswd = 'frontend/usuarios/controller/recuperar_password.php?' . http_build_query($a_cosas);

$a_cosas = ['url_index' => $url_index, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkAyuda2FA = 'frontend/usuarios/controller/ayuda_2fa_reset.php?' . http_build_query($a_cosas);

$url = str_replace('index.php', '', $url_index);
$url_lista_backend = Hash::cmdSinParametros($url.'src/usuarios/infrastructure/controllers/usuario_ayuda_info.php');

$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'ubicacion' => $Qubicacion,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$errores = $data['errores'];
$emailOfuscado = $data['emailOfuscado'];
$mail_admin = $data['mail_admin'];

$a_campos = [
    'error_txt' => $errores,
    'linkEnviarMailPasswd' => $linkEnviarMailPasswd,
    'emailOfuscado' => $emailOfuscado,
    'linkAyuda2FA' => $linkAyuda2FA,
    'mail_admin' => $mail_admin,
    'url_index' => $url_index,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_acceso.phtml', $a_campos);