<?php

use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qemail = (string)filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

$Usuariorepository = new UsuarioRepository();
$oUsuario = $Usuariorepository->findById($Qid_usuario);
$usuario = $oUsuario->getUsuario();
$oUsuario->setEmail($Qemail);
if ($Usuariorepository->Guardar($oUsuario) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $Usuariorepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');