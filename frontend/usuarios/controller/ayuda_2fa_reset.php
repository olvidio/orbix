<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require __DIR__ . '/../../../libs/vendor/autoload.php';

if ($_POST === [] && $_GET !== []) {
    $_POST = $_GET;
}

$Qusername = usuarios_request_string('username');
$Qubicacion = usuarios_request_string('ubicacion');
$Qesquema = usuarios_request_string('esquema');
$Qurl_base = usuarios_request_string('url_base');

$a_cosas = ['url_base' => $Qurl_base, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMail2fa = 'recuperar_2fa.php?' . http_build_query($a_cosas);

$data = usuarios_post_data(PostRequest::getDataFromUrl($Qurl_base . 'src/usuarios/usuario_ayuda_info', [
    'username' => $Qusername,
    'esquema' => $Qesquema,
]));

$a_campos = [
    'error_txt' => tessera_imprimir_string($data['errores'] ?? ''),
    'linkEnviarMail2fa' => $linkEnviarMail2fa,
    'emailOfuscado' => tessera_imprimir_string($data['emailOfuscado'] ?? ''),
    'url_base' => $Qurl_base,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_2fa_reset.phtml', $a_campos);
