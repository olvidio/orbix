<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Página para recuperar el QR para la app 2fa.
 *
 * Genera un token aleatorio, lo guarda en la DB
 * y envia un mail con un link.
 */
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


$mi_ruta = 'src/usuarios/recuperar_2fa_mail';
$url_backend = $Qurl_base . $mi_ruta;

$a_campos_backend = [
    'username' => $Qusername,
    'esquema' => $Qesquema,
    'ubicacion' => $Qubicacion,
    'url_base' => $Qurl_base,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
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
    'url_base' => $Qurl_base,
];

// Renderizar la vista
$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('recuperar_2fa.phtml', $a_campos);