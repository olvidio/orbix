<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use usuarios\model\entity\Usuario;
use web\Hash;

/**
 * Formulario para cambiar el mail por parte del usuario.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$id_usuario = $oMiUsuario->getId_usuario();

//////////////////////// Datos del usuario ///////////////////////////////////////////////////
$url_usuario_form_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/usuarios/controller/usuario_info.php'
);

$oHash = new Hash();
$oHash->setUrl($url_usuario_form_backend);
$oHash->setArrayCamposHidden(
    ['id_usuario' => $id_usuario,
    ]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_usuario_form_backend, $hash_params);

$usuario = $data['usuario'];
$pass = $data['pass'];
$email = $data['email'];

$oHash = new Hash();
$oHash->setCamposForm('email');
$a_camposHidden = array(
    'pass' => $pass,
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

$oView = new ViewPhtml('../frontend/usuarios/controller');
$oView->renderizar('usuario_form_mail.phtml', $a_campos);