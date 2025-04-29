<?php

use Illuminate\Http\JsonResponse;
use permisos\model\MyCrypt;
use src\usuarios\application\repositories\UsuarioRepository;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************


$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qusuario = (string)filter_input(INPUT_POST, 'usuario');
$Qpassword = (string)filter_input(INPUT_POST, 'password');

if (!empty($Qusuario)) { // si es nuevo no tiene id
    $usuario = $Qusuario;
} elseif (!empty($Qid_usuario)) {
    $UsuarioReposiroty = new UsuarioRepository();
    $oUsuario = $UsuarioReposiroty->findById($Qid_usuario);
    $usuario = $oUsuario->getUsuario();
}

if (!empty($Qpassword)) {
    $oCrypt = new MyCrypt();
    $jsondata = $oCrypt->is_valid_password($usuario, $Qpassword);
    (new JsonResponse($jsondata))->send();
    exit();
}