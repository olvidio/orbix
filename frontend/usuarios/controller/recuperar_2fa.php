<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

/**
 * Página para recuperar la contraseña de un usuario.
 * Genera una contraseña aleatoria, marca en la tabla del usuario que debe cambiarla
 * y envía la nueva contraseña por correo electrónico.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qusername = (string)filter_input(INPUT_GET, 'username');
$Qubicacion = (string)filter_input(INPUT_GET, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_GET, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_GET, 'esquema_web');
$Qurl_index = (string)filter_input(INPUT_GET, 'url_index');

// Si no hay username, redirigir a la página de ayuda
if (empty($Qusername)) {
    header("Location: ayuda_acceso.php");
    exit;
}

$url_index = $_SERVER['HTTP_REFERER'];
$url = str_replace('index.php', '', $url_index);
$url_backend = $url . 'src/usuarios/infrastructure/controllers/recuperar_2fa_mail.php';
$a_campos = [
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'ubicacion' => $Qubicacion,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos);

$error_txt = $data['errores'];
$email = $data['email'];
$success = $data['success'];

// Preparar los datos para la vista
$a_campos = [
    'error_txt' => $error_txt,
    'success' => $success,
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'email' => $email,
    'url_index' => $Qurl_index,
];

// Renderizar la vista
$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recuperar_2fa.phtml', $a_campos);