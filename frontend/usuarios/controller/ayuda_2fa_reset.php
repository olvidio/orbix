<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require __DIR__ . '/../../../libs/vendor/autoload.php';

if ($_POST === [] && $_GET !== []) {
    $_POST = $_GET;
}

$Qusername = UsuariosPostInput::requestString('username');
$Qubicacion = UsuariosPostInput::requestString('ubicacion');
$Qesquema = UsuariosPostInput::requestString('esquema');
$Qurl_base = UsuariosPostInput::requestString('url_base');

$a_cosas = ['url_base' => $Qurl_base, 'username' => $Qusername, 'ubicacion' => $Qubicacion, 'esquema' => $Qesquema];
$linkEnviarMail2fa = 'recuperar_2fa.php?' . http_build_query($a_cosas);

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl($Qurl_base . 'src/usuarios/usuario_ayuda_info', [
    'username' => $Qusername,
    'esquema' => $Qesquema,
]));

$a_campos = [
    'error_txt' => PayloadCoercion::string($data['errores'] ?? ''),
    'linkEnviarMail2fa' => $linkEnviarMail2fa,
    'emailOfuscado' => PayloadCoercion::string($data['emailOfuscado'] ?? ''),
    'url_base' => $Qurl_base,
];

$oView = new ViewNewPhtml('frontend\usuarios\view');
$oView->renderizar('ayuda_2fa_reset.phtml', $a_campos);
