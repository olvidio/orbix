<?php
/**
 * Página para recuperar la contraseña de un usuario.
 * Genera una contraseña aleatoria, marca en la tabla del usuario que debe cambiarla
 * y envía la nueva contraseña por correo electrónico.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************


$Qusername = (string)filter_input(INPUT_GET, 'username');
$Qubicacion = (string)filter_input(INPUT_GET, 'ubicacion');
$Qesquema = (string)filter_input(INPUT_GET, 'esquema');
$Qesquema_web = (string)filter_input(INPUT_GET, 'esquema_web');
$Qurl_base = (string)filter_input(INPUT_GET, 'url_base');

// Si no hay username, redirigir a la página de ayuda
if (empty($Qusername)) {
    header("Location: ayuda_acceso.php");
    exit;
}

$url_backend = $Qurl_base . 'src/usuarios/recuperar_password_mail';
$a_campos_backend = [
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'ubicacion' => $Qubicacion,
    'esquema_web' => $Qesquema_web,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$error_txt = $data['error_txt'];
$email = $data['email'];
$success = $data['success'];

// Preparar los datos para la vista
$a_campos = [
    'error_txt' => $error_txt,
    'success' => $success,
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'email' => $email,
    'url_base' => $Qurl_base,
];

// Renderizar la vista
$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recuperar_password.phtml', $a_campos);
